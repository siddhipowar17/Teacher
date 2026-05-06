/* ============================================
   LuxeRent - Main JavaScript
   ============================================ */

$(document).ready(function () {
    // ---- Loading Screen ----
    setTimeout(function () {
        $('#loading-screen').addClass('loaded');
    }, 1800);

    // ---- AOS Init ----
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 80
    });

    // ---- Cursor Glow ----
    const cursorGlow = document.getElementById('cursorGlow');
    if (cursorGlow) {
        document.addEventListener('mousemove', function (e) {
            cursorGlow.style.left = e.clientX + 'px';
            cursorGlow.style.top = e.clientY + 'px';
        });
    }

    // ---- Navbar Scroll ----
    $(window).on('scroll', function () {
        const nav = $('#mainNav');
        if ($(this).scrollTop() > 80) {
            nav.addClass('scrolled');
        } else {
            nav.removeClass('scrolled');
        }

        // Back to top
        if ($(this).scrollTop() > 400) {
            $('#backToTop').addClass('visible');
        } else {
            $('#backToTop').removeClass('visible');
        }
    });

    $('#backToTop').on('click', function () {
        $('html, body').animate({ scrollTop: 0 }, 600);
    });

    // ---- Dark Mode ----
    const darkToggle = document.getElementById('darkModeToggle');
    if (darkToggle) {
        // Load saved preference
        const saved = localStorage.getItem('luxerent-dark-mode');
        if (saved === 'true') {
            document.documentElement.setAttribute('data-theme', 'dark');
            darkToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }

        darkToggle.addEventListener('click', function () {
            const current = document.documentElement.getAttribute('data-theme');
            if (current === 'dark') {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('luxerent-dark-mode', 'false');
                this.innerHTML = '<i class="fas fa-moon"></i>';
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('luxerent-dark-mode', 'true');
                this.innerHTML = '<i class="fas fa-sun"></i>';
            }
        });
    }

    // ---- Live Search ----
    let searchTimeout;
    $('#liveSearch').on('input', function () {
        const query = $(this).val().trim();
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $('#searchResults').removeClass('active').html('');
            return;
        }

        searchTimeout = setTimeout(function () {
            $.ajax({
                url: 'includes/ajax/search.php',
                type: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function (data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(function (item) {
                            html += '<a href="product-details.php?slug=' + item.slug + '" class="search-result-item">';
                            html += '<div class="search-result-info">';
                            html += '<h6>' + item.name + '</h6>';
                            html += '<span>$' + item.price_per_day + '/day</span>';
                            html += '</div></a>';
                        });
                    } else {
                        html = '<div class="p-3 text-center text-muted">No results found</div>';
                    }
                    $('#searchResults').html(html).addClass('active');
                }
            });
        }, 300);
    });

    // Close search on click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-glass').length) {
            $('#searchResults').removeClass('active');
        }
    });

    // ---- Wishlist Toggle (AJAX) ----
    $(document).on('click', '.wishlist-toggle', function (e) {
        e.preventDefault();
        const btn = $(this);
        const productId = btn.data('product-id');

        $.ajax({
            url: 'includes/ajax/wishlist.php',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'added') {
                    btn.addClass('active');
                    showNotification('Added to wishlist', 'success');
                } else if (res.status === 'removed') {
                    btn.removeClass('active');
                    showNotification('Removed from wishlist', 'info');
                } else if (res.status === 'login_required') {
                    window.location.href = 'login.php';
                }
            }
        });
    });

    // ---- Booking Calculator ----
    function calculateTotal() {
        const startDate = new Date($('#startDate').val());
        const endDate = new Date($('#endDate').val());
        const pricePerDay = parseFloat($('#pricePerDay').val());

        if (startDate && endDate && endDate > startDate) {
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const total = (diffDays * pricePerDay).toFixed(2);

            $('#totalDays').text(diffDays);
            $('#totalPrice').text('$' + total);
            $('#totalPriceInput').val(total);
            $('#totalDaysInput').val(diffDays);
        }
    }

    $('#startDate, #endDate').on('change', calculateTotal);

    // ---- Animated Counters ----
    function animateCounters() {
        $('.stat-number[data-count]').each(function () {
            const $this = $(this);
            if ($this.hasClass('counted')) return;

            const target = parseInt($this.data('count'));
            const duration = 2000;
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;

            $this.addClass('counted');

            const timer = setInterval(function () {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                $this.text(formatNumber(Math.floor(current)));
            }, 16);
        });
    }

    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K+';
        }
        return num + '+';
    }

    // Intersection Observer for counters
    const counterSection = document.querySelector('.stats-section');
    if (counterSection) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounters();
                }
            });
        }, { threshold: 0.3 });
        observer.observe(counterSection);
    }

    // ---- Notification System ----
    window.showNotification = function (message, type) {
        type = type || 'info';
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        const colors = {
            success: '#22c55e',
            error: '#f5576c',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const toast = $('<div class="notification-toast">' +
            '<i class="fas ' + icons[type] + '" style="color:' + colors[type] + ';font-size:1.2rem;"></i>' +
            '<span>' + message + '</span>' +
            '</div>');

        $('body').append(toast);

        setTimeout(function () {
            toast.addClass('hide');
            setTimeout(function () { toast.remove(); }, 400);
        }, 3000);
    };

    // ---- Infinite Scroll (Products Page) ----
    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;

    function loadMoreProducts() {
        if (isLoading || !hasMore) return;
        isLoading = true;
        currentPage++;

        const category = new URLSearchParams(window.location.search).get('cat') || '';
        const sort = $('#sortFilter').val() || '';

        $.ajax({
            url: 'includes/ajax/load-products.php',
            type: 'GET',
            data: { page: currentPage, category: category, sort: sort },
            dataType: 'json',
            beforeSend: function () {
                $('#loadingMore').show();
            },
            success: function (res) {
                if (res.html && res.html.trim()) {
                    $('#productsGrid').append(res.html);
                    AOS.refresh();
                } else {
                    hasMore = false;
                }
                isLoading = false;
                $('#loadingMore').hide();
            },
            error: function () {
                isLoading = false;
                $('#loadingMore').hide();
            }
        });
    }

    if ($('#productsGrid').length) {
        $(window).on('scroll', function () {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 500) {
                loadMoreProducts();
            }
        });
    }

    // ---- Filter Change ----
    $('#sortFilter, #categoryFilter, #priceFilter').on('change', function () {
        const params = new URLSearchParams();
        const cat = $('#categoryFilter').val();
        const sort = $('#sortFilter').val();
        const price = $('#priceFilter').val();

        if (cat) params.set('cat', cat);
        if (sort) params.set('sort', sort);
        if (price) params.set('price', price);

        window.location.href = 'products.php?' + params.toString();
    });

    // ---- Swiper Testimonials ----
    if (document.querySelector('.reviews-swiper')) {
        new Swiper('.reviews-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });
    }

    // ---- Product Image Gallery ----
    $('.product-thumb').on('click', function () {
        const src = $(this).find('img').attr('src');
        $('.product-main-image').attr('src', src);
        $('.product-thumb').removeClass('active');
        $(this).addClass('active');
    });
});
