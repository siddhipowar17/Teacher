/* ============================================
   LuxeRent - GSAP + Scroll Animations
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    // Register ScrollTrigger
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);

        // ---- Hero Animations ----
        const heroTl = gsap.timeline({ delay: 1.8 });

        heroTl
            .from('.hero-badge', {
                y: 30, opacity: 0, duration: 0.8, ease: 'power3.out'
            })
            .from('.hero-title', {
                y: 60, opacity: 0, duration: 1, ease: 'power3.out'
            }, '-=0.4')
            .from('.hero-subtitle', {
                y: 40, opacity: 0, duration: 0.8, ease: 'power3.out'
            }, '-=0.5')
            .from('.hero-buttons > *', {
                y: 30, opacity: 0, duration: 0.6, stagger: 0.15, ease: 'power3.out'
            }, '-=0.4')
            .from('.scroll-indicator', {
                y: 20, opacity: 0, duration: 0.6, ease: 'power3.out'
            }, '-=0.2');

        // ---- Hero Parallax on Mouse Move ----
        const heroContent = document.querySelector('.hero-content');
        if (heroContent) {
            document.querySelector('.hero-section')?.addEventListener('mousemove', function (e) {
                const xAxis = (window.innerWidth / 2 - e.pageX) / 40;
                const yAxis = (window.innerHeight / 2 - e.pageY) / 40;
                heroContent.style.transform = 'translateX(' + xAxis + 'px) translateY(' + yAxis + 'px)';
            });
        }

        // ---- Section Reveal Animations ----
        gsap.utils.toArray('.section-header').forEach(function (header) {
            gsap.from(header.children, {
                y: 40,
                opacity: 0,
                duration: 0.8,
                stagger: 0.15,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: header,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                }
            });
        });

        // ---- Product Cards Stagger ----
        gsap.utils.toArray('.products-grid-animated .product-card').forEach(function (card, i) {
            gsap.from(card, {
                y: 50,
                opacity: 0,
                duration: 0.7,
                delay: i * 0.1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: card,
                    start: 'top 85%',
                    toggleActions: 'play none none none'
                }
            });
        });

        // ---- Category Cards ----
        gsap.utils.toArray('.category-card').forEach(function (card, i) {
            gsap.from(card, {
                y: 40,
                opacity: 0,
                duration: 0.6,
                delay: i * 0.08,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: card,
                    start: 'top 85%',
                    toggleActions: 'play none none none'
                }
            });
        });

        // ---- Showcase Split Animation ----
        const showcaseContent = document.querySelector('.showcase-content');
        const showcaseVideo = document.querySelector('.showcase-video-wrapper');

        if (showcaseContent) {
            gsap.from(showcaseContent, {
                x: -80,
                opacity: 0,
                duration: 1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '.showcase-section',
                    start: 'top 70%',
                    toggleActions: 'play none none none'
                }
            });
        }

        if (showcaseVideo) {
            gsap.from(showcaseVideo, {
                x: 80,
                opacity: 0,
                duration: 1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '.showcase-section',
                    start: 'top 70%',
                    toggleActions: 'play none none none'
                }
            });
        }

        // ---- Stats Counter Animation ----
        gsap.utils.toArray('.stat-item').forEach(function (stat, i) {
            gsap.from(stat, {
                y: 30,
                opacity: 0,
                duration: 0.6,
                delay: i * 0.1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '.stats-section',
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                }
            });
        });

        // ---- Footer Animation ----
        gsap.from('.premium-footer .row > div', {
            y: 40,
            opacity: 0,
            duration: 0.7,
            stagger: 0.1,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: '.premium-footer',
                start: 'top 85%',
                toggleActions: 'play none none none'
            }
        });

        // ---- Smooth Section Transitions ----
        gsap.utils.toArray('.section-padding').forEach(function (section) {
            gsap.from(section, {
                opacity: 0,
                duration: 0.5,
                scrollTrigger: {
                    trigger: section,
                    start: 'top 90%',
                    toggleActions: 'play none none none'
                }
            });
        });

        // ---- Parallax Background Elements ----
        gsap.utils.toArray('[data-parallax]').forEach(function (el) {
            const speed = parseFloat(el.dataset.parallax) || 0.3;
            gsap.to(el, {
                yPercent: speed * 100,
                ease: 'none',
                scrollTrigger: {
                    trigger: el,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: true
                }
            });
        });

        // ---- Image Reveal on Scroll ----
        gsap.utils.toArray('.image-reveal').forEach(function (img) {
            ScrollTrigger.create({
                trigger: img,
                start: 'top 80%',
                onEnter: function () {
                    img.classList.add('revealed');
                }
            });
        });

        // ---- Magnetic Button Effect ----
        document.querySelectorAll('.magnetic-btn').forEach(function (btn) {
            btn.addEventListener('mousemove', function (e) {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                gsap.to(btn, {
                    x: x * 0.3,
                    y: y * 0.3,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });

            btn.addEventListener('mouseleave', function () {
                gsap.to(btn, { x: 0, y: 0, duration: 0.5, ease: 'elastic.out(1, 0.5)' });
            });
        });
    }

    // ---- Smooth Scroll for Anchor Links ----
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
