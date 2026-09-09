<script>
    "use strict";

    /**
     * REEXPAY API Documentation JavaScript
     * Professional API documentation with responsive sidebar and smooth navigation
     */

    class APIDocumentation {
        constructor() {
            this.sidebar = document.getElementById('apiSidebar');
            this.sidebarOverlay = document.getElementById('sidebarOverlay');
            this.sidebarToggle = document.getElementById('sidebarToggle');
            this.scrollTopBtn = document.getElementById('scrollTopBtn');
            this.navLinks = document.querySelectorAll('.sidebar-link[href^="#"]');
            
            this.init();
        }

        init() {
            this.setupSidebarToggle();
            this.setupSmoothScrolling();
            this.setupScrollSpy();
            this.setupScrollToTop();
            this.setupLanguageTabs();
            this.setupResponsiveHandling();
            
            // Initialize on load
            this.updateActiveNavigation();
            
            // Initialize Highlight.js and code features
            this.initializeHighlightJS();
            
            console.log('REEXPAY API Documentation initialized successfully');
        }

        /**
         * Setup sidebar toggle functionality for mobile
         */
        setupSidebarToggle() {
            if (this.sidebarToggle) {
                this.sidebarToggle.addEventListener('click', () => {
                    this.toggleSidebar();
                });
            }

            if (this.sidebarOverlay) {
                this.sidebarOverlay.addEventListener('click', () => {
                    this.closeSidebar();
                });
            }

            // Close sidebar on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.sidebar?.classList.contains('show')) {
                    this.closeSidebar();
                }
            });
        }

        /**
         * Toggle sidebar visibility
         */
        toggleSidebar() {
            if (this.sidebar && this.sidebarOverlay) {
                const isOpen = this.sidebar.classList.contains('show');
                
                if (isOpen) {
                    this.closeSidebar();
                } else {
                    this.openSidebar();
                }
            }
        }

        /**
         * Open sidebar
         */
        openSidebar() {
            if (this.sidebar && this.sidebarOverlay) {
                this.sidebar.classList.add('show');
                this.sidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        /**
         * Close sidebar
         */
        closeSidebar() {
            if (this.sidebar && this.sidebarOverlay) {
                this.sidebar.classList.remove('show');
                this.sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        /**
         * Setup smooth scrolling for navigation links
         */
        setupSmoothScrolling() {
            this.navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    const targetId = link.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        // Close sidebar on mobile after navigation
                        this.closeSidebar();
                        
                        // Smooth scroll to target
                        const headerOffset = 90; // Account for fixed header
                        const elementPosition = targetElement.offsetTop;
                        const offsetPosition = elementPosition - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        // Update URL without jumping
                        history.pushState(null, null, targetId);
                    }
                });
            });
        }

        /**
         * Setup scroll spy for active navigation highlighting
         */
        setupScrollSpy() {
            const sections = document.querySelectorAll('.content-section[id]');
            let isScrolling = false;

            const updateActiveNavigation = () => {
                if (isScrolling) return;

                const scrollPos = window.scrollY + 100;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;
                let current = '';

                // Check if we're at the bottom of the page
                const isAtBottom = scrollPos + windowHeight >= documentHeight - 50;

                sections.forEach((section, index) => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    const sectionId = section.getAttribute('id');
                    
                    // For the last section or if we're at the bottom, make it easier to activate
                    if (index === sections.length - 1 && (isAtBottom || scrollPos >= sectionTop - 200)) {
                        current = sectionId;
                    }
                    // Normal sections
                    else if (scrollPos >= sectionTop - 100 && scrollPos < sectionTop + sectionHeight) {
                        current = sectionId;
                    }
                });

                // Fallback: if no section is active and we have sections, activate the first one
                if (!current && sections.length > 0) {
                    current = sections[0].getAttribute('id');
                }

                // Update active navigation link
                this.navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${current}`) {
                        link.classList.add('active');
                    }
                });
            };

            // Throttled scroll handler
            let scrollTimer = null;
            window.addEventListener('scroll', () => {
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }
                
                scrollTimer = setTimeout(() => {
                    updateActiveNavigation();
                }, 10);
            });

            // Initial update
            updateActiveNavigation();
        }

        /**
         * Update active navigation on page load
         */
        updateActiveNavigation() {
            const hash = window.location.hash;
            if (hash) {
                this.navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === hash) {
                        link.classList.add('active');
                    }
                });
            }
        }

        /**
         * Setup scroll to top button
         */
        setupScrollToTop() {
            if (!this.scrollTopBtn) return;

            // Show/hide scroll to top button
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    this.scrollTopBtn.classList.add('show');
                } else {
                    this.scrollTopBtn.classList.remove('show');
                }
            });

            // Scroll to top functionality
            this.scrollTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        /**
         * Setup language tabs for code examples
         */
        setupLanguageTabs() {
            const tabContainers = document.querySelectorAll('.language-tabs');
            
            tabContainers.forEach(container => {
                const tabs = container.querySelectorAll('.nav-link');
                const panes = container.querySelectorAll('.tab-pane');
                
                tabs.forEach(tab => {
                    tab.addEventListener('click', (e) => {
                        e.preventDefault();
                        
                        const targetId = tab.getAttribute('href');
                        const targetPane = document.querySelector(targetId);
                        
                        if (targetPane) {
                            // Remove active classes
                            tabs.forEach(t => t.classList.remove('active'));
                            panes.forEach(p => p.classList.remove('active', 'show'));
                            
                            // Add active classes
                            tab.classList.add('active');
                            targetPane.classList.add('active', 'show');
                            
                            // Re-highlight code in the new tab with Highlight.js
                            if (typeof hljs !== 'undefined') {
                                targetPane.querySelectorAll('pre code').forEach(block => {
                                    hljs.highlightElement(block);
                                });
                            }
                        }
                    });
                });
            });
        }

        /**
         * Setup responsive handling
         */
        setupResponsiveHandling() {
            let resizeTimer = null;
            
            window.addEventListener('resize', () => {
                if (resizeTimer) {
                    clearTimeout(resizeTimer);
                }
                
                resizeTimer = setTimeout(() => {
                    // Close sidebar on desktop resize
                    if (window.innerWidth >= 992) {
                        this.closeSidebar();
                    }
                }, 250);
            });
        }

        /**
         * Initialize Highlight.js for syntax highlighting
         */
        initializeHighlightJS() {
            const initHighlight = () => {
                if (typeof hljs !== 'undefined') {
                    try {
                        // Highlight all code blocks
                        hljs.highlightAll();
                        
                        // Add copy buttons after highlighting
                        this.setupCodeCopyButtons();
                        
                        console.log('Highlight.js syntax highlighting initialized');
                    } catch (error) {
                        console.error('Highlight.js initialization error:', error);
                        // Fallback: setup copy buttons anyway
                        this.setupCodeCopyButtons();
                    }
                } else {
                    // Highlight.js not loaded yet, setup copy buttons as fallback
                    this.setupCodeCopyButtons();
                    
                    // Retry Highlight.js initialization
                    setTimeout(initHighlight, 300);
                }
            };
            
            // Initialize after a short delay to ensure DOM is ready
            setTimeout(initHighlight, 100);
        }

        /**
         * Setup code copy buttons
         */
        setupCodeCopyButtons() {
            // Add copy buttons to code blocks
            const codeBlocks = document.querySelectorAll('pre code');
            
            codeBlocks.forEach(codeElement => {
                const preElement = codeElement.parentElement;
                
                // Skip if already has copy button
                if (preElement.parentElement.querySelector('.copy-btn')) {
                    return;
                }
                
                const copyBtn = document.createElement('button');
                copyBtn.className = 'copy-btn btn btn-sm';
                copyBtn.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                copyBtn.style.cssText = `
                    position: absolute;
                    top: 1rem;
                    right: 1rem;
                    background: rgba(37, 99, 235, 0.8);
                    color: white;
                    border: none;
                    border-radius: 0.375rem;
                    padding: 0.25rem 0.5rem;
                    font-size: 0.75rem;
                    z-index: 10;
                    cursor: pointer;
                `;

                copyBtn.addEventListener('click', () => {
                    const codeContent = codeElement.textContent || codeElement.innerText;
                    this.copyToClipboard(codeContent, copyBtn);
                });

                // Make parent relative for absolute positioning
                preElement.parentElement.style.position = 'relative';
                preElement.parentElement.appendChild(copyBtn);
            });
        }

        /**
         * Copy text to clipboard
         */
        async copyToClipboard(text, button) {
            try {
                await navigator.clipboard.writeText(text);
                
                // Update button state
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                button.style.background = 'rgba(16, 185, 129, 0.8)';
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.style.background = 'rgba(37, 99, 235, 0.8)';
                }, 2000);
                
            } catch (err) {
                console.error('Failed to copy text:', err);
                
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                try {
                    document.execCommand('copy');
                    button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                    setTimeout(() => {
                        button.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                    }, 2000);
                } catch (fallbackErr) {
                    console.error('Fallback copy failed:', fallbackErr);
                }
                
                document.body.removeChild(textArea);
            }
        }

        /**
         * Utility method to highlight code syntax
         */
        highlightCode() {
            if (typeof hljs !== 'undefined') {
                hljs.highlightAll();
            }
        }
    }

    /**
     * Initialize everything when DOM is loaded
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize main API documentation
        const apiDocs = new APIDocumentation();
        
        // Smooth scroll for hash links on page load
        if (window.location.hash) {
            setTimeout(() => {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    const headerOffset = 90;
                    const elementPosition = target.offsetTop;
                    const offsetPosition = elementPosition - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }, 100);
        }
    });

</script>