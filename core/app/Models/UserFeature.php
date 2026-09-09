<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeature extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'feature', 'description', 'status', 'sort_order'];

    /**
     * Sync features for a specific user in the order defined in the config file.
     */
    public static function syncWithConfigForUser($userId): void
    {
        $configFeatures = config('userFeatures.features');

        $existingFeatures   = self::where('user_id', $userId)->pluck('feature')->toArray();
        $configFeatureNames = array_column($configFeatures, 'feature');

        // ✅ Insert or Update Features for the User in Order
        foreach ($configFeatures as $index => $configFeature) {
            self::updateOrCreate(
                ['user_id' => $userId, 'feature' => $configFeature['feature']],
                [
                    'description' => $configFeature['description'],
                    'status'      => $configFeature['status'],
                    'sort_order'  => $index, // Order is based on config file index
                ]
            );
        }

        // ✅ Remove Features No Longer in Config for the User
        $featuresToRemove = array_diff($existingFeatures, $configFeatureNames);
        if (! empty($featuresToRemove)) {
            self::where('user_id', $userId)->whereIn('feature', $featuresToRemove)->delete();
        }
    }

    /**
     * Accessor: Dynamically return the feature status based on user verification
     */
    public function getDynamicStatusAttribute()
    {
        $user = $this->user; // Fetch the related user

        if (! $user) {
            return $this->status; // If user is missing, return stored status
        }

        // ✅ Override status based on user verification
        if ($this->feature === 'email_verification') {
            return $user->email_verified_at !== null;
        }

        if ($this->feature === 'kyc_verification') {
            return (bool) $user->isKycVerified();
        }

        return $this->status; // Default behavior
    }

    /**
     * Relationship: UserFeature belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
