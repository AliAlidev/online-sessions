// Constants
const TRANS_EVENTS = ['transitionend', 'webkitTransitionEnd', 'oTransitionEnd'];
const TRANS_PROPERTIES = ['transition', 'MozTransition', 'webkitTransition', 'WebkitTransition', 'OTransition'];
const INLINE_STYLES = `
.layout-menu-fixed .layout-navbar-full .layout-menu,
.layout-page {
  padding-top: {navbarHeight}px !important;
}
.content-wrapper {
  padding-bottom: {footerHeight}px !important;
}
`;

// Helper Functions
function requiredParam(name) {
    throw new Error(`Parameter required${name ? `: \`${name}\`` : ''}`);
}

// Main Helpers Object
const Helpers = {
    // Root Element
    ROOT_EL: typeof window !== 'undefined' ? document.documentElement : null,

    // Large screens breakpoint
    LAYOUT_BREAKPOINT: 1200,

    // Resize delay in milliseconds
    RESIZE_DELAY: 200,

    // Internal variables
    _curStyle: null,
    _styleEl: null,
    _resizeTimeout: null,
    _resizeCallback: null,
    _transitionCallback: null,
    _transitionCallbackTimeout: null,
    _listeners: [],
    _initialized: false,
    _autoUpdate: false,
    _lastWindowHeight: 0,

    // *******************************************************************************
    // * Utilities

    // Scroll To Active Menu Item
    _scrollToActive(animate = false, duration = 500) {
        const layoutMenu = this.getLayoutMenu();
        if (!layoutMenu) return;

        const activeEl = layoutMenu.querySelector('li.menu-item.active:not(.open)');
        if (activeEl) {
            const easeInOutQuad = (t, b, c, d) => {
                t /= d / 2;
                if (t < 1) return (c / 2) * t * t + b;
                t--;
                return (-c / 2) * (t * (t - 2) - 1) + b;
            };

            const element = layoutMenu.querySelector('.menu-inner');
            const start = element.scrollTop;
            const change = activeEl.getBoundingClientRect().top + element.scrollTop - parseInt(element.clientHeight / 2, 10);
            const startDate = +new Date();

            if (animate) {
                const animateScroll = () => {
                    const currentDate = +new Date();
                    const currentTime = currentDate - startDate;
                    const val = easeInOutQuad(currentTime, start, change, duration);
                    element.scrollTop = val;
                    if (currentTime < duration) {
                        requestAnimationFrame(animateScroll);
                    } else {
                        element.scrollTop = change;
                    }
                };
                animateScroll();
            } else {
                element.scrollTop = change;
            }
        }
    },

    // Add classes
    _addClass(cls, el = this.ROOT_EL) {
        if (el && el.length !== undefined) {
            el.forEach(e => {
                if (e) {
                    cls.split(' ').forEach(c => e.classList.add(c));
                }
            });
        } else if (el) {
            cls.split(' ').forEach(c => el.classList.add(c));
        }
    },

    // Remove classes
    _removeClass(cls, el = this.ROOT_EL) {
        if (el && el.length !== undefined) {
            el.forEach(e => {
                if (e) {
                    cls.split(' ').forEach(c => e.classList.remove(c));
                }
            });
        } else if (el) {
            cls.split(' ').forEach(c => el.classList.remove(c));
        }
    },

    // Toggle classes
    _toggleClass(el = this.ROOT_EL, cls1, cls2) {
        if (el.classList.contains(cls1)) {
            el.classList.replace(cls1, cls2);
        } else {
            el.classList.replace(cls2, cls1);
        }
    },

    // Has class
    _hasClass(cls, el = this.ROOT_EL) {
        let result = false;
        cls.split(' ').forEach(c => {
            if (el.classList.contains(c)) result = true;
        });
        return result;
    },

    // Find parent with class
    _findParent(el, cls) {
        if (el && el.tagName.toUpperCase() === 'BODY' || el.tagName.toUpperCase() === 'HTML') return null;
        el = el.parentNode;
        while (el && el.tagName.toUpperCase() !== 'BODY' && !el.classList.contains(cls)) {
            el = el.parentNode;
        }
        return el && el.tagName.toUpperCase() !== 'BODY' ? el : null;
    },

    // Trigger window event
    _triggerWindowEvent(name) {
        if (typeof window === 'undefined') return;
        if (document.createEvent) {
            let event;
            if (typeof Event === 'function') {
                event = new Event(name);
            } else {
                event = document.createEvent('Event');
                event.initEvent(name, false, true);
            }
            window.dispatchEvent(event);
        } else {
            window.fireEvent(`on${name}`, document.createEventObject());
        }
    },

    // Trigger event
    _triggerEvent(name) {
        this._triggerWindowEvent(`layout${name}`);
        this._listeners
            .filter(listener => listener.event === name)
            .forEach(listener => listener.callback.call(null));
    },

    // Update inline styles
    _updateInlineStyle(navbarHeight = 0, footerHeight = 0) {
        if (!this._styleEl) {
            this._styleEl = document.createElement('style');
            this._styleEl.type = 'text/css';
            document.head.appendChild(this._styleEl);
        }
        const newStyle = INLINE_STYLES.replace(/\{navbarHeight\}/gi, navbarHeight).replace(/\{footerHeight\}/gi, footerHeight);
        if (this._curStyle !== newStyle) {
            this._curStyle = newStyle;
            this._styleEl.textContent = newStyle;
        }
    },

    // Remove inline styles
    _removeInlineStyle() {
        if (this._styleEl) document.head.removeChild(this._styleEl);
        this._styleEl = null;
        this._curStyle = null;
    },

    // Redraw layout menu (Safari bugfix)
    _redrawLayoutMenu() {
        const layoutMenu = this.getLayoutMenu();
        if (layoutMenu && layoutMenu.querySelector('.menu')) {
            const inner = layoutMenu.querySelector('.menu-inner');
            const scrollTop = inner.scrollTop;
            const pageScrollTop = document.documentElement.scrollTop;
            layoutMenu.style.display = 'none';
            layoutMenu.style.display = '';
            inner.scrollTop = scrollTop;
            document.documentElement.scrollTop = pageScrollTop;
            return true;
        }
        return false;
    },

    // Check for transition support
    _supportsTransitionEnd() {
        if (window.QUnit) return false;
        const el = document.body || document.documentElement;
        if (!el) return false;
        let result = false;
        TRANS_PROPERTIES.forEach(evnt => {
            if (typeof el.style[evnt] !== 'undefined') result = true;
        });
        return result;
    },

    // Calculate current navbar height
    _getNavbarHeight() {
        const layoutNavbar = this.getLayoutNavbar();
        if (!layoutNavbar) return 0;
        if (!this.isSmallScreen()) return layoutNavbar.getBoundingClientRect().height;

        // Logic for small screens
        const clonedEl = layoutNavbar.cloneNode(true);
        clonedEl.id = null;
        clonedEl.style.visibility = 'hidden';
        clonedEl.style.position = 'absolute';
        Array.prototype.slice.call(clonedEl.querySelectorAll('.collapse.show')).forEach(el => this._removeClass('show', el));
        layoutNavbar.parentNode.insertBefore(clonedEl, layoutNavbar);
        const navbarHeight = clonedEl.getBoundingClientRect().height;
        clonedEl.parentNode.removeChild(clonedEl);
        return navbarHeight;
    },

    // Get current footer height
    _getFooterHeight() {
        const layoutFooter = this.getLayoutFooter();
        if (!layoutFooter) return 0;
        return layoutFooter.getBoundingClientRect().height;
    },

    // Get animation duration of element
    _getAnimationDuration(el) {
        const duration = window.getComputedStyle(el).transitionDuration;
        return parseFloat(duration) * (duration.indexOf('ms') !== -1 ? 1 : 1000);
    },

    // Set menu hover state
    _setMenuHoverState(hovered) {
        this[hovered ? '_addClass' : '_removeClass']('layout-menu-hover');
    },

    // Toggle collapsed
    _setCollapsed(collapsed) {
        if (this.isSmallScreen()) {
            if (collapsed) {
                this._removeClass('layout-menu-expanded');
            } else {
                setTimeout(() => {
                    this._addClass('layout-menu-expanded');
                }, this._redrawLayoutMenu() ? 5 : 0);
            }
        }
    },

    // Bind layout animation end event
    _bindLayoutAnimationEndEvent(modifier, cb) {
        const menu = this.getMenu();
        const duration = menu ? this._getAnimationDuration(menu) + 50 : 0;
        if (!duration) {
            modifier.call(this);
            cb.call(this);
            return;
        }
        this._transitionCallback = e => {
            if (e.target !== menu) return;
            this._unbindLayoutAnimationEndEvent();
            cb.call(this);
        };
        TRANS_EVENTS.forEach(e => {
            menu.addEventListener(e, this._transitionCallback, false);
        });
        modifier.call(this);
        this._transitionCallbackTimeout = setTimeout(() => {
            this._transitionCallback.call(this, { target: menu });
        }, duration);
    },

    // Unbind layout animation end event
    _unbindLayoutAnimationEndEvent() {
        const menu = this.getMenu();
        if (this._transitionCallbackTimeout) {
            clearTimeout(this._transitionCallbackTimeout);
            this._transitionCallbackTimeout = null;
        }
        if (menu && this._transitionCallback) {
            TRANS_EVENTS.forEach(e => {
                menu.removeEventListener(e, this._transitionCallback, false);
            });
        }
        if (this._transitionCallback) {
            this._transitionCallback = null;
        }
    },

    // Bind delayed window resize event
    _bindWindowResizeEvent() {
        this._unbindWindowResizeEvent();
        const cb = () => {
            if (this._resizeTimeout) {
                clearTimeout(this._resizeTimeout);
                this._resizeTimeout = null;
            }
            this._triggerEvent('resize');
        };
        this._resizeCallback = () => {
            if (this._resizeTimeout) clearTimeout(this._resizeTimeout);
            this._resizeTimeout = setTimeout(cb, this.RESIZE_DELAY);
        };
        window.addEventListener('resize', this._resizeCallback, false);
    },

    // Unbind delayed window resize event
    _unbindWindowResizeEvent() {
        if (this._resizeTimeout) {
            clearTimeout(this._resizeTimeout);
            this._resizeTimeout = null;
        }
        if (this._resizeCallback) {
            window.removeEventListener('resize', this._resizeCallback, false);
            this._resizeCallback = null;
        }
    },

    // Bind menu mouse events
    _bindMenuMouseEvents() {
        if (this._menuMouseEnter && this._menuMouseLeave && this._windowTouchStart) return;
        const layoutMenu = this.getLayoutMenu();
        if (!layoutMenu) return this._unbindMenuMouseEvents();

        if (!this._menuMouseEnter) {
            this._menuMouseEnter = () => {
                if (this.isSmallScreen() || this._hasClass('layout-transitioning')) {
                    return this._setMenuHoverState(false);
                }
                return this._setMenuHoverState(false);
            };
            layoutMenu.addEventListener('mouseenter', this._menuMouseEnter, false);
            layoutMenu.addEventListener('touchstart', this._menuMouseEnter, false);
        }

        if (!this._menuMouseLeave) {
            this._menuMouseLeave = () => {
                this._setMenuHoverState(false);
            };
            layoutMenu.addEventListener('mouseleave', this._menuMouseLeave, false);
        }

        if (!this._windowTouchStart) {
            this._windowTouchStart = e => {
                if (!e || !e.target || !this._findParent(e.target, '.layout-menu')) {
                    this._setMenuHoverState(false);
                }
            };
            window.addEventListener('touchstart', this._windowTouchStart, true);
        }
    },

    // Unbind menu mouse events
    _unbindMenuMouseEvents() {
        if (!this._menuMouseEnter && !this._menuMouseLeave && !this._windowTouchStart) return;
        const layoutMenu = this.getLayoutMenu();
        if (this._menuMouseEnter) {
            if (layoutMenu) {
                layoutMenu.removeEventListener('mouseenter', this._menuMouseEnter, false);
                layoutMenu.removeEventListener('touchstart', this._menuMouseEnter, false);
            }
            this._menuMouseEnter = null;
        }
        if (this._menuMouseLeave) {
            if (layoutMenu) {
                layoutMenu.removeEventListener('mouseleave', this._menuMouseLeave, false);
            }
            this._menuMouseLeave = null;
        }
        if (this._windowTouchStart) {
            if (layoutMenu) {
                window.addEventListener('touchstart', this._windowTouchStart, true);
            }
            this._windowTouchStart = null;
        }
        this._setMenuHoverState(false);
    },

    // *******************************************************************************
    // * Methods

    // Scroll to active menu item
    scrollToActive(animate = false) {
        this._scrollToActive(animate);
    },

    // Collapse/expand layout
    setCollapsed(collapsed = requiredParam('collapsed'), animate = true) {
        const layoutMenu = this.getLayoutMenu();
        if (!layoutMenu) return;
        this._unbindLayoutAnimationEndEvent();
        if (animate && this._supportsTransitionEnd()) {
            this._addClass('layout-transitioning');
            if (collapsed) this._setMenuHoverState(false);
            this._bindLayoutAnimationEndEvent(() => {
                if (this.isSmallScreen) this._setCollapsed(collapsed);
            }, () => {
                this._removeClass('layout-transitioning');
                this._triggerWindowEvent('resize');
                this._triggerEvent('toggle');
                this._setMenuHoverState(false);
            });
        } else {
            this._addClass('layout-no-transition');
            if (collapsed) this._setMenuHoverState(false);
            this._setCollapsed(collapsed);
            setTimeout(() => {
                this._removeClass('layout-no-transition');
                this._triggerWindowEvent('resize');
                this._triggerEvent('toggle');
                this._setMenuHoverState(false);
            }, 1);
        }
    },

    // Toggle layout
    toggleCollapsed(animate = true) {
        this.setCollapsed(!this.isCollapsed(), animate);
    },

    // Set layout positioning
    setPosition(fixed = requiredParam('fixed'), offcanvas = requiredParam('offcanvas')) {
        this._removeClass('layout-menu-offcanvas layout-menu-fixed layout-menu-fixed-offcanvas');
        if (!fixed && offcanvas) {
            this._addClass('layout-menu-offcanvas');
        } else if (fixed && !offcanvas) {
            this._addClass('layout-menu-fixed');
            this._redrawLayoutMenu();
        } else if (fixed && offcanvas) {
            this._addClass('layout-menu-fixed-offcanvas');
            this._redrawLayoutMenu();
        }
        this.update();
    },

    // *******************************************************************************
    // * Getters

    // Get layout menu element
    getLayoutMenu() {
        return document.querySelector('.layout-menu');
    },

    // Get menu element
    getMenu() {
        const layoutMenu = this.getLayoutMenu();
        if (!layoutMenu) return null;
        return !this._hasClass('menu', layoutMenu) ? layoutMenu.querySelector('.menu') : layoutMenu;
    },

    // Get layout navbar element
    getLayoutNavbar() {
        return document.querySelector('.layout-navbar');
    },

    // Get layout footer element
    getLayoutFooter() {
        return document.querySelector('.content-footer');
    },

    // *******************************************************************************
    // * Update

    // Update layout
    update() {
        if (this.getLayoutNavbar() && (!this.isSmallScreen() && this.isLayoutNavbarFull() && this.isFixed() || this.isNavbarFixed()) || this.getLayoutFooter() && this.isFooterFixed()) {
            this._updateInlineStyle(this._getNavbarHeight(), this._getFooterHeight());
        }
        this._bindMenuMouseEvents();
    },

    // Set auto update
    setAutoUpdate(enable = requiredParam('enable')) {
        if (enable && !this._autoUpdate) {
            this.on('resize.Helpers:autoUpdate', () => this.update());
            this._autoUpdate = true;
        } else if (!enable && this._autoUpdate) {
            this.off('resize.Helpers:autoUpdate');
            this._autoUpdate = false;
        }
    },

    // *******************************************************************************
    // * Tests

    // Check if RTL
    isRtl() {
        return document.querySelector('body').getAttribute('dir') === 'rtl' || document.querySelector('html').getAttribute('dir') === 'rtl';
    },

    // Check if mobile device
    isMobileDevice() {
        return typeof window.orientation !== 'undefined' || navigator.userAgent.indexOf('IEMobile') !== -1;
    },

    // Check if small screen
    isSmallScreen() {
        return (window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth) < this.LAYOUT_BREAKPOINT;
    },

    // Check if layout navbar is full
    isLayoutNavbarFull() {
        return !!document.querySelector('.layout-wrapper.layout-navbar-full');
    },

    // Check if layout is collapsed
    isCollapsed() {
        if (this.isSmallScreen()) {
            return !this._hasClass('layout-menu-expanded');
        }
        return this._hasClass('layout-menu-collapsed');
    },

    // Check if layout is fixed
    isFixed() {
        return this._hasClass('layout-menu-fixed layout-menu-fixed-offcanvas');
    },

    // Check if navbar is fixed
    isNavbarFixed() {
        return this._hasClass('layout-navbar-fixed') || !this.isSmallScreen() && this.isFixed() && this.isLayoutNavbarFull();
    },

    // Check if footer is fixed
    isFooterFixed() {
        return this._hasClass('layout-footer-fixed');
    },

    // Check if light style is applied
    isLightStyle() {
        return document.documentElement.classList.contains('light-style');
    },

    // *******************************************************************************
    // * Events

    // Add event listener
    on(event = requiredParam('event'), callback = requiredParam('callback')) {
        const [eventName, ...namespace] = event.split('.');
        this._listeners.push({
            event: eventName,
            namespace: namespace.join('.') || null,
            callback: callback
        });
    },

    // Remove event listener
    off(event = requiredParam('event')) {
        const [eventName, ...namespace] = event.split('.');
        this._listeners = this._listeners.filter(listener => !(listener.event === eventName && listener.namespace === namespace.join('.') || null));
    },

    // *******************************************************************************
    // * Life cycle

    // Initialize
    init() {
        if (this._initialized) return;
        this._initialized = true;

        // Initialize `style` element
        this._updateInlineStyle(0);

        // Bind window resize event
        this._bindWindowResizeEvent();

        // Bind init event
        this.off('init._Helpers');
        this.on('init._Helpers', () => {
            this.off('resize._Helpers:redrawMenu');
            this.on('resize._Helpers:redrawMenu', () => {
                if (this.isSmallScreen() && !this.isCollapsed()) this._redrawLayoutMenu();
            });

            // Force repaint in IE 10
            if (typeof document.documentMode === 'number' && document.documentMode < 11) {
                this.off('resize._Helpers:ie10RepaintBody');
                this.on('resize._Helpers:ie10RepaintBody', () => {
                    if (this.isFixed()) return;
                    const scrollTop = document.documentElement.scrollTop;
                    document.body.style.display = 'none';
                    document.body.style.display = 'block';
                    document.documentElement.scrollTop = scrollTop;
                });
            }
        });
        this._triggerEvent('init');
    },

    // Destroy
    destroy() {
        if (!this._initialized) return;
        this._initialized = false;
        this._removeClass('layout-transitioning');
        this._removeInlineStyle();
        this._unbindLayoutAnimationEndEvent();
        this._unbindWindowResizeEvent();
        this._unbindMenuMouseEvents();
        this.setAutoUpdate(false);
        this.off('init._Helpers');

        // Remove all listeners except `init`
        this._listeners = this._listeners.filter(listener => listener.event !== 'init');
    },

    // *******************************************************************************
    // * Initialization

    // Initialize password toggle
    initPasswordToggle() {
        const toggler = document.querySelectorAll('.form-password-toggle i');
        if (toggler) {
            toggler.forEach(el => {
                el.addEventListener('click', e => {
                    e.preventDefault();
                    const formPasswordToggle = el.closest('.form-password-toggle');
                    const formPasswordToggleIcon = formPasswordToggle.querySelector('i');
                    const formPasswordToggleInput = formPasswordToggle.querySelector('input');
                    if (formPasswordToggleInput.getAttribute('type') === 'text') {
                        formPasswordToggleInput.setAttribute('type', 'password');
                        formPasswordToggleIcon.classList.replace('bx-show', 'bx-hide');
                    } else if (formPasswordToggleInput.getAttribute('type') === 'password') {
                        formPasswordToggleInput.setAttribute('type', 'text');
                        formPasswordToggleIcon.classList.replace('bx-hide', 'bx-show');
                    }
                });
            });
        }
    },

    // Initialize speech-to-text
    initSpeechToText() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const speechToText = document.querySelectorAll('.speech-to-text');
        if (SpeechRecognition && speechToText) {
            const recognition = new SpeechRecognition();
            const toggler = document.querySelectorAll('.speech-to-text i');
            toggler.forEach(el => {
                let listening = false;
                el.addEventListener('click', () => {
                    el.closest('.input-group').querySelector('.form-control').focus();
                    recognition.onspeechstart = () => (listening = true);
                    if (!listening) recognition.start();
                    recognition.onerror = () => (listening = false);
                    recognition.onresult = event => {
                        el.closest('.input-group').querySelector('.form-control').value = event.results[0][0].transcript;
                    };
                    recognition.onspeechend = () => {
                        listening = false;
                        recognition.stop();
                    };
                });
            });
        }
    },

    // Ajax call
    ajaxCall(url) {
        return new Promise((resolve, reject) => {
            const req = new XMLHttpRequest();
            req.open('GET', url);
            req.onload = () => (req.status === 200 ? resolve(req.response) : reject(Error(req.statusText)));
            req.onerror = e => reject(Error(`Network Error: ${e}`));
            req.send();
        });
    },

    // Initialize sidebar toggle
    initSidebarToggle() {
        const sidebarToggler = document.querySelectorAll('[data-bs-toggle="sidebar"]');
        sidebarToggler.forEach(el => {
            el.addEventListener('click', () => {
                const target = el.getAttribute('data-target');
                const overlay = el.getAttribute('data-overlay');
                const appOverlay = document.querySelectorAll('.app-overlay');
                const targetEl = document.querySelectorAll(target);
                targetEl.forEach(tel => {
                    tel.classList.toggle('show');
                    if (overlay && appOverlay) {
                        if (tel.classList.contains('show')) {
                            appOverlay[0].classList.add('show');
                        } else {
                            appOverlay[0].classList.remove('show');
                        }
                        appOverlay[0].addEventListener('click', e => {
                            e.currentTarget.classList.remove('show');
                            tel.classList.remove('show');
                        });
                    }
                });
            });
        });
    }
};

// *******************************************************************************
// * Initialization

if (typeof window !== 'undefined') {
    Helpers.init();
    if (Helpers.isMobileDevice() && window.chrome) {
        document.documentElement.classList.add('layout-menu-100vh');
    }

    // Update layout after page load
    if (document.readyState === 'complete') Helpers.update();
    else document.addEventListener('DOMContentLoaded', () => Helpers.update());
}

// Expose Helpers to the global scope
window.Helpers = Helpers;
