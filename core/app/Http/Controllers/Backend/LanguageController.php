<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Language\StoreRequest;
use App\Http\Requests\Language\UpdateRequest;
use App\Models\Language;
use App\Traits\FileManageTrait;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JoeDixon\Translation\Drivers\Translation;
use Throwable;

class LanguageController extends BaseController
{
    use AuthorizesRequests;
    use FileManageTrait;

    private Translation $translation;

    private string $languageFilesPath;

    public function __construct(Translation $translation)
    {
        $this->translation       = $translation;
        $this->languageFilesPath = resource_path('lang');
    }

    public static function permissions(): array
    {
        return [
            'index'                                          => 'language-list',
            'store'                                          => 'language-create',
            'edit|update|destroy|translate|translatedUpdate' => 'language-manage',
        ];
    }

    /**
     * Display a paginated listing of the Language resource.
     */
    public function index()
    {

        $languages = Language::paginate(10);

        return view('backend.languages.index', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $input = $request->validated();

        if (isset($input['is_default']) && $input['is_default']) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $data = [
            'flag'       => self::uploadImage($input['flag']),
            'name'       => $input['language_name'],
            'code'       => Str::lower($input['language_code']),
            'is_default' => $input['is_default'] ?? 0,
            'status'     => $input['status']     ?? 0,
        ];

        $this->translation->addLanguage($input['language_code'], $input['language_name']);
        Language::create($data);

        notifyEvs('success', __('Language Added Successfully'));

        return redirect()->route('admin.language.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws Throwable
     */
    public function edit($id)
    {
        $language = Language::find($id);

        return view('backend.languages.edit', compact('language'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $input    = $request->validated();
        $language = Language::find($id);

        if (! isset($input['is_default']) && $language->is_default) {
            notifyEvs('error', 'Must have one default language');

            return redirect()->route('admin.language.index');
        }

        if (isset($input['is_default']) && $input['is_default'] !== $language->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        if (! isset($input['status']) && $language->is_default) {
            notifyEvs('error', 'Default language must be active');

            return redirect()->route('admin.language.index');
        }
        $languageCode = $language->code === 'en' ? $language->code : Str::lower($input['language_code']);
        $data         = [
            'name'       => $input['language_name'],
            'code'       => $languageCode,
            'is_default' => $input['is_default'] ?? 0,
            'status'     => $input['status']     ?? 0,
        ];

        if (isset($input['flag'])) {
            $data['flag'] = self::uploadImage($input['flag'], $language->flag);
        }

        // Extract filename and directory name using pathinfo()
        $filePathInfo   = pathinfo("{$this->languageFilesPath}/{$language->code}.json");
        $folderPathInfo = pathinfo("{$this->languageFilesPath}/{$language->code}");

        // Build new file and directory paths
        $newFileName   = "{$this->languageFilesPath}/{$languageCode}.json";
        $newFolderName = "{$this->languageFilesPath}/{$languageCode}";

        // Rename both file and directory
        rename($filePathInfo['dirname'].'/'.$filePathInfo['basename'], $newFileName);
        rename($folderPathInfo['dirname'].'/'.$folderPathInfo['basename'], $newFolderName);

        $language->update($data);

        notifyEvs('success', 'Language Updated Successfully');

        return redirect()->route('admin.language.index');

    }

    public function translate(Request $request, $languageCode)
    {

        if ($request->has('language') && $request->get('language') !== $languageCode) {
            return redirect()
                ->route('admin.language.translate', ['code' => $request->get('language'), 'group' => $request->get('group'), 'filter' => $request->get('filter')]);
        }

        $languages = $this->translation->allLanguages();

        $groups = $this->translation->getGroupsFor(config('app.fallback_locale'))->merge('single');

        $translations = $this->translation->filterTranslationsFor($languageCode, $request->get('filter'));

        if ($request->filled('group')) {
            $group = $request->get('group');

            if ($group === 'single') {
                $translations = new Collection(['single' => $translations->get('single')]);
            } else {
                $translations = new Collection(['group' => $translations->get('group')->only($group)]);
            }
        }

        return view('backend.languages.translate', compact('languageCode', 'languages', 'groups', 'translations'));

    }

    public function translatedUpdate(Request $request)
    {

        $keyGroup           = $request->group;
        $language           = $request->language;
        $isGroupTranslation = ! Str::contains($keyGroup, 'single');

        $this->translation->add($request, $language, $isGroupTranslation);

        notifyEvs('success', __('Keyword Updated Successfully'));

        return redirect()->back();
    }

    public function syncMissingKeys()
    {
        Artisan::call('translation:sync-missing-translation-keys');
        notifyEvs('success', __('Translation Keys Synced Successfully'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $language = Language::find($id);

        try {
            $this->authorize('delete', $language);

            $languageCode          = $language->code;
            $languageFilePath      = "{$this->languageFilesPath}/$languageCode.json";
            $languageDirectoryPath = "{$this->languageFilesPath}/$languageCode";

            // Delete the language file and directory
            File::deleteDirectory($languageDirectoryPath);
            File::delete($languageFilePath);

            // Delete the language record from the database
            self::delete($language->flag);
            $language->delete();

            $keys = [
                'home_page_components',
                'header_navigations',
                'footer_navigations',
                'services',
                'blogs',
                'languages',
                'languagesPluck',
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }
            notifyEvs('success', 'Language Deleted Successfully');

            return redirect()->route('admin.language.index');

        } catch (Exception $e) {
            notifyEvs('error', $e->getMessage());

            return redirect()->back();
        }

    }
}
