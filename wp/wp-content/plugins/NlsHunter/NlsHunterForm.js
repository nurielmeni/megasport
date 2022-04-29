var nls =
  nls ||
  (function ($) {
    "use strict";

    var emptyFriendDetails = "";
    var jobOptions;

    var Validators = {
      ISRID: {
        fn: function (value) {
          // DEFINE RETURN VALUES
          var R_ELEGAL_INPUT = false; // -1
          var R_NOT_VALID = false; // -2
          var R_VALID = true; // 1

          //INPUT VALIDATION

          // Just in case -> convert to string
          var IDnum = String(value);

          // Validate correct input (Changed from 5 to 9 so only 9 digits are allowed)
          if (IDnum.length > 9 || IDnum.length < 9) return R_ELEGAL_INPUT;
          if (isNaN(IDnum)) return R_ELEGAL_INPUT;

          // The number is too short - add leading 0000
          if (IDnum.length < 9) {
            while (IDnum.length < 9) {
              IDnum = "0" + IDnum;
            }
          }

          // CHECK THE ID NUMBER
          var mone = 0,
            incNum;
          for (var i = 0; i < 9; i++) {
            incNum = Number(IDnum.charAt(i));
            incNum *= (i % 2) + 1;
            if (incNum > 9) incNum -= 9;
            mone += incNum;
          }
          if (mone % 10 == 0) return R_VALID;
          else return R_NOT_VALID;
        },
        msg: "מספר הזהות לא חוקי",
      },

      email: {
        fn: function (value) {
          var fnxMail = value + '@fnx.co.il';
          var regex =
            /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return value && regex.test(String(fnxMail).toLowerCase());
        },
        msg: "כתובת האימייל לא חוקית",
      },

      phone: {
        fn: function (value) {
          var regex = /^05[0-9][-]{0,1}[0-9]{3}[-]{0,1}[0-9]{4}$/i;
          return value && regex.test(String(value).trim().toLowerCase());
        },
        msg: "מספר הטלפון לא חוקי",
      },

      required: {
        fn: function (value) {
          return value && value.length > 0;
        },
        msg: "שדה זה הוא שדה חובה",
      },

      // If no option was selected of radi will return false
      radioRequired: {
        fn: function (el) {
          var valid = false;
          var name = $(el).attr("name");
          if (typeof name === "undefined") return valid;

          $(el)
            .parents(".nls-apply-field")
            .find('input[name="' + name + '"]')
            .each(function (i, option) {
              if ($(option).prop("checked")) valid = true;
            });
          return valid;
        },
        msg: "יש לבחור אחת מהאפשרויות",
      },
    };

    var validateSubmit = function (form) {
      clearValidation(form);
      var valid = true;

      $(form)
        .find("input")
        .each(function (i, el) {
          if ($(el).parents(".nls-apply-field").css("display") === "none")
            return;
          if (typeof $(el).attr("validator") === "undefined") return;
          if (!fieldValidate(el)) valid = false;
        });
      console.log("Valid: ", valid);
      validForm(form);
      return valid;
    };

    var validForm = function (formFields) {
      var invalidFields = $(formFields).find(".nls-invalid");
      if (invalidFields.length > 0) {
        $(".nls-apply-for-jobs .form-footer .help-block")
          .text("אחד או יותר משדות הטופס לא תקין")
          .show();
      } else {
        $(".nls-apply-for-jobs .form-footer .help-block").hide();
        $(".nls-apply-for-jobs .help-block").text("");
      }
    };

    // Validates all of the field validators
    var fieldValidate = function (el) {
      var valid = true;
      var validatorAttr = $(el).attr("validator");
      var validators = validatorAttr.trim().split(" ");
      var type = $(el).attr("type");
      var value = type === "radio" ? el : $(el).val();

      validators.forEach(function (validator) {
        // If invalid skip (show only first error)
        if ($(el).hasClass("nls-invalid")) return;

        if (!Validators[validator].fn(value)) {
          valid = false;
          var invalidElement =
            type === "radio" ? $(el).parents(".options-wrapper") : $(el);

          $(invalidElement).addClass("nls-invalid");
          $(el)
            .parents(".nls-apply-field")
            .find(".help-block")
            .text(Validators[validator].msg);
        }
      });
      return valid;
    };

    var clearFields = function (form) {
      form.find('input:not([type="radio"],[type="hidden"])').val("");
      clearValidation(form);
    };

    var clearValidation = function (form) {
      $(form).find(".nls-invalid").removeClass("nls-invalid");
      $(form).find(".nls-apply-field .help-block").text("");
      validForm();
    };

    var clearFieldValidation = function (el) {
      $(el)
        .parents(".nls-apply-field")
        .find(".nls-invalid")
        .removeClass("nls-invalid");
      $(el).parents(".nls-apply-field").find(".help-block").text("");
    };

    var getParam = function (param) {
      var queryString = window.location.search;
      var urlParams = new URLSearchParams(queryString);
      return urlParams.get(param);
    };

    var hideBeforeApply = function () {
      $(".nls-apply-for-jobs").hide();
      $("section.nls-fbf-flow-wrapper").hide();
    };

    var showHomePage = function () {
      $("#apply-response").remove();
      $(".nls-reply-message").remove();
      $(".hide-response-success").show();
      $(".hide-response-error").show();
      $(".nls-apply-for-jobs").show();
      $("section.nls-fbf-flow-wrapper").show();
    };

    var showLastApply = function () {
      $('.form-body button.apply-cv').hide();
      $('.form-body button.apply-cv:last').show();
    }

    $(document).ready(function () {
      // Share button init Share Api
      typeof ShareApi !== 'undefined' && ShareApi.init({ shareButton: 'button[target="share"]' });

      // Set the sid if exist
      getParam("sid") && $('input[name="sid"').val(getParam("sid"));

      // Save an element with all the availiable options
      jobOptions = document.createElement('select');
      jQuery(jobOptions).append(jQuery('select#friend-job-code--0 > option').clone());

      // Add event listeners
      console.log("Ready Function");

      // Category changed
      $(document).on('change', '.category-option', function () {
        var categoryCode = $(this).val();
        if (categoryCode.length < 1) return;
        var $target = $(this).parents('.form-body').find('.job-option');
        var $options = $(jobOptions).clone().children('[category="' + categoryCode + '"]');
        $target.empty().append($options);
      });

      // Initilize the select options
      $('.category-option').trigger('change');

      // Apply selected jobs
      $(document).on(
        "click",
        "button.apply-cv",
        function (event) {
          event.preventDefault();

          var applyCvButton = event.target;
          var form = $('form.nls-apply-for-jobs');
          var formData = new FormData(form[0]);

          if (!validateSubmit(form)) {
            $('form.nls-apply-for-jobs .nls-invalid').get(0).scrollIntoView();
            return;
          }

          formData.append("action", "apply_cv_function");

          $.ajax({
            url: frontend_ajax.url,
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
              hideBeforeApply();
              $(".nls-apply-for-jobs").after(
                '<div id="apply-response" class="nls-rounded-10 nls-box-shadow"><div id="nls-loader" class="loader">אנא המתן...</div></div>'
              );
              var offset = $("#apply-response").offset();
              $("html, body").animate({
                scrollTop: offset.top - 100,
              });
            },
            success: function (response) {
              $("#nls-loader").remove();
              console.log("Status: ", response.sent);

              if (response.sent > 0) {
                $(".hide-response-success").hide();
                $("#apply-response").remove();
                $("article .entry-content > *").hide();
                $("article .entry-content").append(response.html);
              } else {
                $(".hide-response-error").hide();
                $("#apply-response").html(response.html);
              }
              $(document.body).trigger("post-load");
            },
            complete: function () {
              window.history.pushState({}, "/");
            },
            type: "POST",
          });

        }
      );

      // State handler
      window.addEventListener("popstate", function (event) {
        if (event.state === null) {
          $(".submit-response").remove();
          $("article .entry-content > *").show();
          $(".nls-apply-for-jobs").show();
          $("section.nls-fbf-flow-wrapper").show();
        }
      });

      // Apply friend button handler
      $(".apply-friend a").on("click", function () {
        $(".employee-details")[0].scrollIntoView({ behavior: "smooth" });
        $("input[name='employee-name']")[0].focus();
      });

      // Create an empty element
      emptyFriendDetails = $(
        ".nls-apply-for-jobs .friends-details .friends-container"
      ).html();

      // Add new friend hendler
      $(document).on("click", ".nls-apply-field a.add-friend",
        function () {
          var count = $(
            ".nls-apply-for-jobs .friends-details .friends-container .form-body"
          ).length;
          var needle = /--0/g;
          var re = "--" + count;

          $(emptyFriendDetails.replace(needle, re))
            .hide()
            .appendTo(".nls-apply-for-jobs .friends-details .friends-container")
            .show("slow");
          showLastApply();
        }
      );

      // Toggle share/recomend job
      $('.fbf-actions button').on('click', function () {
        var target = $(this).attr('target');
        if (target === 'recomend') {
          $('.share-el').slideUp();
          $('.recomend-el').slideDown(500, function () {
            $(this).css('display', $(this).hasClass('flex') ? 'felx' : 'block')
          }).get(0).scrollIntoView();
        } else if (target === 'share') {
          $('.recomend-el').slideUp();
        }
      });


      // Remove friend
      $(document).on(
        "click",
        ".nls-apply-for-jobs .friends-details .friends-container .form-body span.remove",
        function () {
          var target = $(this).parent();
          $(target).hide("slow", function () {
            $(target).remove();
            showLastApply();
          });
        }
      );

      // Add file indication when selected
      $(document).on(
        "change",
        '.nls-apply-for-jobs input[type="file"]',
        function (e) {
          if ($(this).val().length > 0) {
            $(this).parent().find("label").addClass("file-selected");
          } else {
            $(this).parent().find("label").removeClass("file-selected");
          }
        }
      );

      // Clear validation errors on focus
      $(document).on("focus", "input", function () {
        clearFieldValidation(this);
      });

      // Validate on blur and change
      $(document).on("blur change", 'input:not([type="radio"])', function () {
        if (typeof $(this).attr("validator") === "undefined") return;
        clearFieldValidation(this);
        fieldValidate(this);
        validForm();
      });

      // Toggle visibility of radio
      $(document).on("change", 'input[type="radio"]', function () {
        var showClass = ".nls-apply-field." + $(this).attr("name") + "-show";
        $('input[name="' + $(this).attr("name") + '"]').prop("checked")
          ? $(showClass).show()
          : $(showClass).hide();
      });

      // Make sure to initilize the radio display options
      $('input[type="radio"]').trigger("change");
    });

    return {
      clearFields: clearFields,
      validateSubmit: validateSubmit
    };
  })(jQuery);
