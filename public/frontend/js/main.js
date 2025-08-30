(function ($) {
    "user strict";

    //preloder
    $(window).on('load', function () {
        $(".preloader").delay(1500).animate({
            "opacity": "0"
        }, 1500, function () {
            $(".preloader").css("display", "none");
        });
    });

    //Create Background Image
    (function background() {
        let img = $('.bg_img');
        img.css('background-image', function () {
            var bg = ('url(' + $(this).data('background') + ')');
            return bg;
        });
    })();

    // aos
    AOS.init();

    // header-fixed
    var fixed_top = $(".header-section");
    $(window).on("scroll", function () {
        if ($(window).scrollTop() > 100) {
            fixed_top.addClass("animated fadeInDown header-fixed");
        }
        else {
            fixed_top.removeClass("animated fadeInDown header-fixed");
        }
    });

    // nice-select
    $(".nice-select").niceSelect(),

        // lightcase
        $(window).on('load', function () {
            $("a[data-rel^=lightcase]").lightcase();
        })

    // navbar-click
    $(".navbar li a").on("click", function () {
        var element = $(this).parent("li");
        if (element.hasClass("show")) {
            element.removeClass("show");
            element.children("ul").slideUp(500);
        }
        else {
            element.siblings("li").removeClass('show');
            element.addClass("show");
            element.siblings("li").find("ul").slideUp(500);
            element.children('ul').slideDown(500);
        }
    });


    // scroll-to-top
    var ScrollTop = $(".scrollToTop");
    $(window).on('scroll', function () {
        if ($(this).scrollTop() < 100) {
            ScrollTop.removeClass("active");
        } else {
            ScrollTop.addClass("active");
        }
    });


    //Odometer
    if ($(".counter").length) {
        $(".counter").each(function () {
            $(this).isInViewport(function (status) {
                if (status === "entered") {
                    for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
                        var el = document.querySelectorAll('.odometer')[i];
                        el.innerHTML = el.getAttribute("data-odometer-final");
                    }
                }
            });
        });
    }

    //toggle passwoard

    $(".toggle-password").click(function () {
        $(this).toggleClass("la-eye la-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    //toggle passwoard
    $(document).ready(function () {
        $(".show_hide_password a").on('click', function (event) {
            event.preventDefault();
            if ($('.show_hide_password input').attr("type") == "text") {
                $('.show_hide_password input').attr('type', 'password');
                $('.show_hide_password i').addClass("fa-eye-slash");
                $('.show_hide_password i').removeClass("fa-eye");
            } else if ($('.show_hide_password input').attr("type") == "password") {
                $('.show_hide_password input').attr('type', 'text');
                $('.show_hide_password i').removeClass("fa-eye-slash");
                $('.show_hide_password i').addClass("fa-eye");
            }
        });
    });
    $(document).ready(function () {
        $(".show_hide_password-2 a").on('click', function (event) {
            event.preventDefault();
            if ($('.show_hide_password-2 input').attr("type") == "text") {
                $('.show_hide_password-2 input').attr('type', 'password');
                $('.show_hide_password-2 i').addClass("fa-eye-slash");
                $('.show_hide_password-2 i').removeClass("fa-eye");
            } else if ($('.show_hide_password-2 input').attr("type") == "password") {
                $('.show_hide_password-2 input').attr('type', 'text');
                $('.show_hide_password-2 i').removeClass("fa-eye-slash");
                $('.show_hide_password-2 i').addClass("fa-eye");
            }
        });
    });
    $(document).ready(function () {
        $(".show_hide_password-3 a").on('click', function (event) {
            event.preventDefault();
            if ($('.show_hide_password-3 input').attr("type") == "text") {
                $('.show_hide_password-3 input').attr('type', 'password');
                $('.show_hide_password-3 i').addClass("fa-eye-slash");
                $('.show_hide_password-3 i').removeClass("fa-eye");
            } else if ($('.show_hide_password-3 input').attr("type") == "password") {
                $('.show_hide_password-3 input').attr('type', 'text');
                $('.show_hide_password-3 i').removeClass("fa-eye-slash");
                $('.show_hide_password-3 i').addClass("fa-eye");
            }
        });
    });

    // faq
    $('.faq-wrapper .faq-title').on('click', function (e) {
        var element = $(this).parent('.faq-item');
        if (element.hasClass('open')) {
            element.removeClass('open');
            element.find('.faq-content').removeClass('open');
            element.find('.faq-content').slideUp(300, "swing");
        } else {
            element.addClass('open');
            element.children('.faq-content').slideDown(300, "swing");
            element.siblings('.faq-item').children('.faq-content').slideUp(300, "swing");
            element.siblings('.faq-item').removeClass('open');
            element.siblings('.faq-item').find('.faq-title').removeClass('open');
            element.siblings('.taq-item').find('.faq-content').slideUp(300, "swing");
        }
    });

    // slider

    var swiper = new Swiper(".testimonial-slider", {
        slidesPerView: 2,
        spaceBetween: 30,
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        autoplay: {
            speed: 1000,
            delay: 3000,
        },
        speed: 1000,
        breakpoints: {
            1199: {
                slidesPerView: 1,
            },
            991: {
                slidesPerView: 1,
            },
            767: {
                slidesPerView: 1,
            },
            575: {
                slidesPerView: 1,
            },
        }
    });


    //

    $(document).ready(function () {
        var AFFIX_TOP_LIMIT = 300;
        var AFFIX_OFFSET = 110;

        var $menu = $("#menu"),
            $btn = $("#menu-toggle");

        $("#menu-toggle").on("click", function () {
            $menu.toggleClass("open");
            return false;
        });


        $(".docs-nav").each(function () {
            var $affixNav = $(this),
                $container = $affixNav.parent(),
                affixNavfixed = false,
                originalClassName = this.className,
                current = null,
                $links = $affixNav.find("a");

            function getClosestHeader(top) {
                var last = $links.first();

                if (top < AFFIX_TOP_LIMIT) {
                    return last;
                }

                for (var i = 0; i < $links.length; i++) {
                    var $link = $links.eq(i),
                        href = $link.attr("href");

                    if (href.charAt(0) === "#" && href.length > 1) {
                        var $anchor = $(href).first();

                        if ($anchor.length > 0) {
                            var offset = $anchor.offset();

                            if (top < offset.top - AFFIX_OFFSET) {
                                return last;
                            }

                            last = $link;
                        }
                    }
                }
                return last;
            }


            $(window).on("scroll", function (evt) {
                var top = window.scrollY,
                    height = $affixNav.outerHeight(),
                    max_bottom = $container.offset().top + $container.outerHeight(),
                    bottom = top + height + AFFIX_OFFSET;

                if (affixNavfixed) {
                    if (top <= AFFIX_TOP_LIMIT) {
                        $affixNav.removeClass("fixed");
                        $affixNav.css("top", 0);
                        affixNavfixed = false;
                    } else if (bottom > max_bottom) {
                        $affixNav.css("top", (max_bottom - height) - top);
                    } else {
                        $affixNav.css("top", AFFIX_OFFSET);
                    }
                } else if (top > AFFIX_TOP_LIMIT) {
                    $affixNav.addClass("fixed");
                    affixNavfixed = true;
                }

                var $current = getClosestHeader(top);

                if (current !== $current) {
                    $affixNav.find(".active").removeClass("active");
                    $current.addClass("active");
                    current = $current;
                }
            });
        });
    });

    // sidebar
    $(".sidebar-menu-item > a").on("click", function () {
        var element = $(this).parent("li");
        if (element.hasClass("active")) {
            element.removeClass("active");
            element.children("ul").slideUp(500);
        }
        else {
            element.siblings("li").removeClass('active');
            element.addClass("active");
            element.siblings("li").find("ul").slideUp(500);
            element.children('ul').slideDown(500);
        }
    });

    // active menu JS
    function splitSlash(data) {
        return data.split('/').pop();
    }
    function splitQuestion(data) {
        return data.split('?').shift().trim();
    }
    var pageNavLis = $('.sidebar-menu a');
    var dividePath = splitSlash(window.location.href);
    var divideGetData = splitQuestion(dividePath);
    var currentPageUrl = divideGetData;

    // find current sidevar element
    $.each(pageNavLis, function (index, item) {
        var anchoreTag = $(item);
        var anchoreTagHref = $(item).attr('href');
        var index = anchoreTagHref.indexOf('/');
        var getUri = "";
        if (index != -1) {
            // split with /
            getUri = splitSlash(anchoreTagHref);
            getUri = splitQuestion(getUri);
        } else {
            getUri = splitQuestion(anchoreTagHref);
        }
        if (getUri == currentPageUrl) {
            var thisElementParent = anchoreTag.parents('.sidebar-menu-item');
            (anchoreTag.hasClass('nav-link') == true) ? anchoreTag.addClass('active') : thisElementParent.addClass('active');
            (anchoreTag.parents('.sidebar-dropdown')) ? anchoreTag.parents('.sidebar-dropdown').addClass('active') : '';
            (thisElementParent.find('.sidebar-submenu')) ? thisElementParent.find('.sidebar-submenu').slideDown("slow") : '';
            return false;
        }
    });

    //sidebar Menu
    $('.sidebar-menu-bar').on('click', function (e) {
        e.preventDefault();
        if ($('.sidebar, .navbar-wrapper, .body-wrapper').hasClass('active')) {
            $('.sidebar, .navbar-wrapper, .body-wrapper').removeClass('active');
            $('.body-overlay').removeClass('active');
        } else {
            $('.sidebar, .navbar-wrapper, .body-wrapper').addClass('active');
            $('.body-overlay').addClass('active');
        }
    });
    $('#body-overlay').on('click', function (e) {
        e.preventDefault();
        $('.sidebar, .navbar-wrapper, .body-wrapper').removeClass('active');
        $('.body-overlay').removeClass('active');
    });

    // dashboard-list
    $('.dashboard-list-item').on('click', function (e) {
        var element = $(this).parent('.dashboard-list-item-wrapper');
        if (element.hasClass('show')) {
            element.removeClass('show');
            element.find('.preview-list-wrapper').removeClass('show');
            element.find('.preview-list-wrapper').slideUp(300, "swing");
        } else {
            element.addClass('show');
            element.children('.preview-list-wrapper').slideDown(300, "swing");
            element.siblings('.dashboard-list-item-wrapper').children('.preview-list-wrapper').slideUp(300, "swing");
            element.siblings('.dashboard-list-item-wrapper').removeClass('show');
            element.siblings('.dashboard-list-item-wrapper').find('.dashboard-list-item').removeClass('show');
            element.siblings('.dashboard-list-item-wrapper').find('.preview-list-wrapper').slideUp(300, "swing");
        }
    });

    // invoice-form
    $('.invoice-form').on('click', '.add-row-btn', function () {
        $('.add-row-btn').closest('.invoice-form').find('.add-row-wrapper').last().clone().show().appendTo('.results');
    });

    $(document).on('click', '.invoice-cross-btn', function (e) {
        e.preventDefault();
        $(this).parent().parent().hide(300);
    });

    //pdf
    $('.pdf').on('click', function (e) {
        e.preventDefault();
        $('.pdf-area').addClass('active');
        $('.body-overlay').addClass('active');
    });
    $('#body-overlay, #pdf-area').on('click', function (e) {
        e.preventDefault();
        $('.pdf-area').removeClass('active');
        $('.body-overlay').removeClass('active');
    })

    //Notification
    $('.notification-icon').on('click', function (e) {
        e.preventDefault();
        if ($('.notification-wrapper').hasClass('active')) {
            $('.notification-wrapper').removeClass('active');
            $('.body-overlay').removeClass('show');
        } else {
            $('.notification-wrapper').addClass('active');
            $('.body-overlay').addClass('show');
        }
    });
    $('#body-overlay').on('click', function (e) {
        e.preventDefault();
        $('.notification-wrapper').removeClass('active');
        $('.body-overlay').removeClass('show');
    });


    //Profile Upload
    function proPicURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = $(input).parents('.preview-thumb').find('.profilePicPreview');
                $(preview).css('background-image', 'url(' + e.target.result + ')');
                $(preview).addClass('has-image');
                $(preview).hide();
                $(preview).fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".profilePicUpload").on('change', function () {
        proPicURL(this);
    });

    $(".remove-image").on('click', function () {
        $(".profilePicPreview").css('background-image', 'none');
        $(".profilePicPreview").removeClass('has-image');
    });


    //info-btn
    $(document).on('click', '.info-btn', function () {
        $('.support-profile-wrapper').addClass('active');
    });
    $(document).on('click', '.chat-cross-btn', function () {
        $('.support-profile-wrapper').removeClass('active');
    });


    $(document).on("click", ".card-custom", function () {
        $(this).toggleClass("active");
    });

    //acoount-toggle
    $('.header-account-btn').on('click', function (e) {
        e.preventDefault();
        $('.account-section').addClass('active');
        $('.body-overlay').addClass('active');
    });
    $('#body-overlay').on('click', function (e) {
        e.preventDefault();
        $('.account-section').removeClass('active');
        $('.body-overlay').removeClass('active');
    });
    $('.account-close').on('click', function (e) {
        e.preventDefault();
        $('.account-section').removeClass('active');
        $('.body-overlay').removeClass('active');
    });
    $('.remove-account').on('click', function (e) {
        e.preventDefault();
        $(this).parent().parent().hide(300);
    });
    $('.account-control-btn').on('click', function () {
        $('.account-area').toggleClass('change-form');
    })


    $(".account-control-btn").click(function () {
        var source = $(this).attr("data-block");
        $(".account-wrapper").hide();
        $(".account-wrapper." + source).show();
    });



    // select-2 init
    $('.select2-basic').select2();
    $('.select2-multi-select').select2();
    $(".select2-auto-tokenize").select2({
        tags: true,
        tokenSeparators: [',']
    });

    // Checkbox
    $(document).on("change", ".dependency-checkbox", function () {
        dependencyCheckboxHandle($(this));
    });

    $(document).ready(function () {
        let dependencyCheckbox = $(".dependency-checkbox");
        $.each(dependencyCheckbox, function (index, item) {
            dependencyCheckboxHandle($(item));
        });
    });


    function dependencyCheckboxHandle(targetCheckbox) {
        let target = $(targetCheckbox).attr("data-target");
        if ($(targetCheckbox).is(":checked")) {
            $("." + target).slideDown(300);
        } else {
            $("." + target).slideUp(300);
        }
    }

    // login-register-account
    $(".login-btn").click(function () {
        $("#register").addClass("d-none");
        $("#login").removeClass("d-none");
        $(".login-header-top").removeClass("d-none");
        $(".register-header-top").addClass("d-none");
        $(this).addClass("active");
        $(".register-btn").removeClass("active");
    });

    $(".register-btn").click(function () {
        $("#register").removeClass("d-none");
        $("#login").addClass("d-none");
        $(".login-header-top").addClass("d-none");
        $(".register-header-top").removeClass("d-none");
        $(this).addClass("active");
        $(".login-btn").removeClass("active");
    });


    // account type select
    $(document).ready(function () {
        $('.account-type').change(function () {
            var selectedAccountType = $(this).val();
            $('.select-account').hide();
            $('.select-account[data-select-target="' + selectedAccountType + '"]').show();
        });
    });


})(jQuery);

/**
 * Function For Get All Country list by AJAX Request
 * @param {HTML DOM} targetElement
 * @param {Error Place Element} errorElement
 * @returns
 */
var allCountries = "";
function getAllCountries(hitUrl, targetElement = $(".country-select"), errorElement = $(".country-select").siblings(".select2")) {
    if (targetElement.length == 0) {
        return false;
    }
    var CSRF = $("meta[name=csrf-token]").attr("content");
    var data = {
        _token: CSRF,
    };
    $.post(hitUrl, data, function () {
        // success
        $(errorElement).removeClass("is-invalid");
        $(targetElement).siblings(".invalid-feedback").remove();
    }).done(function (response) {
        // Place States to States Field
        var options = "<option selected disabled>Select Country</option>";
        var selected_old_data = "";
        if ($(targetElement).attr("data-old") != null) {
            selected_old_data = $(targetElement).attr("data-old");
        }
        $.each(response, function (index, item) {
            options += `<option value="${item.name}" data-id="${item.id}" data-mobile-code="${item.mobile_code}" data-currency-name="${item.currency_name}" data-currency-code="${item.currency_code}" data-currency-symbol="${item.currency_symbol}" ${selected_old_data == item.name ? "selected" : ""}>${item.name}</option>`;
        });

        allCountries = response;

        $(targetElement).html(options);
    }).fail(function (response) {
        var faildMessage = "Something went wrong! Please try again.";
        var faildElement = `<span class="invalid-feedback" role="alert">
                              <strong>${faildMessage}</strong>
                          </span>`;
        $(errorElement).addClass("is-invalid");
        if ($(targetElement).siblings(".invalid-feedback").length != 0) {
            $(targetElement).siblings(".invalid-feedback").text(faildMessage);
        } else {
            errorElement.after(faildElement);
        }
    });
}

function placePhoneCode(code) {
    if (code != undefined) {
        code = code.replace("+", "");
        code = "+" + code;
        $("input.phone-code").val(code);
        $("div.phone-code").html(code);
    }
}

/**
 * Function for make ajax request for switcher
 * @param {HTML DOM} inputName
 * @param {AJAX URL} hitUrl
 * @param {URL METHOD} method
 */
function switcherAjax(hitUrl, method = "PUT") {
    $(document).on("click", ".event-ready", function(event) {
        var inputName = $(this).parents(".switch-toggles").find("input").attr("name");
        if (inputName == undefined || inputName == "") {
            return false;
        }

        $(this).parents(".switch-toggles").find(".switch").removeClass("event-ready");
        var input = $(this).parents(".switch-toggles").find("input[name=" + inputName + "]");
        var eventElement = $(this);
        if (input.length == 0) {
            alert("Input field not found.");
            $(this).parents(".switch-toggles").find(".switch").addClass("event-ready");
            $(this).find(".btn-ring").hide();
            return false;
        }

        var CSRF = $("head meta[name=csrf-token]").attr("content");

        var dataTarget = "";
        if (input.attr("data-target")) {
            dataTarget = input.attr("data-target");
        }

        var inputValue = input.val();
        var data = {
            _token: CSRF,
            _method: method,
            data_target: dataTarget,
            status: inputValue,
            input_name: inputName,
        };

        $.post(hitUrl, data, function(response) {
            throwMessage('success', response.message.success);
            // Remove Loading animation
            $(event.target).find(".btn-ring").hide();
        }).done(function(response) {
            $(eventElement).parents(".switch-toggles").find(".switch").addClass("event-ready");

            $(eventElement).parents(".switch-toggles.btn-load").toggleClass('active');
            var dataValue = $(eventElement).parents(".switch-toggles").find(".switch").last().attr("data-value");
            if ($(eventElement).parents(".switch-toggles").hasClass("active")) {
                dataValue = $(eventElement).parents(".switch-toggles").find(".switch").first().attr("data-value");
                $(eventElement).parents(".switch-toggles").find(".switch").first().find(".btn-ring").hide();
            }
            $(eventElement).parents(".switch-toggles.btn-load").find("input").val(dataValue);
            $(eventElement).parents(".switch-toggles").find(".switch").find(".btn-ring").hide();

            // Show or hide car-details based on switch state
            var carDetails = $(eventElement).closest('.car-item').find('.car-details');
            if (inputValue == 0) {
                carDetails.css({
                    opacity: "1",
                    pointerEvents: "auto"
                });
            } else {
                carDetails.css({
                    opacity: "0.3",
                    pointerEvents: "none"
                });
            }

        }).fail(function(response) {
            var response = JSON.parse(response.responseText);
            throwMessage(response.type, response.message.error);

            $(eventElement).parents(".switch-toggles").find(".switch").addClass("event-ready");
            $(eventElement).parents(".switch-toggles").find(".btn-ring").hide();
            return false;
        });

    });
}

  function openModalBySelector(selector,animation = "mfp-move-horizontal") {
    $(selector).addClass("white-popup mfp-with-anim");
    if(animation == null) {
      animation = "mfp-zoom-in"
    }
    $.magnificPopup.open({
      removalDelay: 500,
      items: {
        src: $(selector), // can be a HTML string, jQuery object, or CSS selector
        type: 'inline',
      },

      callbacks: {
        beforeOpen: function() {
          this.st.mainClass = animation;
        },
        elementParse: function(item) {
          var modalCloseBtn = $(selector).find(".modal-close");
          $(modalCloseBtn).click(function() {
            $.magnificPopup.close();
          });
        },
      },
    });
    $.magnificPopup.instance._onFocusIn = function(e) {
      // Do nothing if target element is select2 input
      if( $(e.target).hasClass('select2-search__field') ) {
          return true;
      }
      // Else call parent method
      $.magnificPopup.proto._onFocusIn.call(this,e);
    }
  }


