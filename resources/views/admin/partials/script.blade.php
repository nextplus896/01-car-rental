<script>
    var storedHtmlMarkup = {
  add_money_automatic_gateway_credentials_field: `<div class="row align-items-end">
    <div class="col-xl-3 col-lg-3 form-group">
        <label>{{ __("Title") }}*</label>
        <input type="text" class="form--control" placeholder="{{ __("Write Here") }}..." name="title[]">
    </div>
    <div class="col-xl-3 col-lg-3 form-group">
        <label>{{ __("Name") }}*</label>
        <input type="text" class="form--control" placeholder="{{ __("Write Here") }}..." name="name[]">
    </div>
    <div class="col-xl-5 col-lg-5 form-group">
        <label>{{ __("Value") }}</label>
        <input type="text" class="form--control" placeholder="{{ __("Write Here") }}..." name="value[]">
    </div>

    <div class="col-xl-1 col-lg-1 form-group">
        <button type="button" class="custom-btn btn--base btn--danger row-cross-btn w-100"><i class="las la-times"></i></button>
    </div>
  </div>`,
  payment_gateway_currency_block: `<div class="custom-card mt-15 gateway-currency" style="display:none;">
  <div class="card-header">
      <h6 class="currency-title"></h6>
  </div>
  <div class="card-body">
    <div class="row align-items-center">
        <div class="col-xl-2 col-lg-2 form-group">
            <label>{{ __("Gateway Image") }}</label>
            <input type="file" class="file-holder image" name="" accept="image/*">
        </div>
        <div class="col-xl-3 col-lg-3 mb-10">
            <div class="custom-inner-card">
                <div class="card-inner-header">
                    <h5 class="title">{{ __("Amount Limit") }}*</h5>
                </div>
                <div class="card-inner-body">
                    <div class="row">
                        <div class="col-xxl-12 col-xl-6 col-lg-6 form-group">
                            <div class="form-group">
                                <label>{{ __("Minimum") }}</label>
                                <div class="input-group">
                                    <input type="number" class="form--control min-limit" value="0" name="" step="any">
                                    <span class="input-group-text currency"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12 col-xl-6 col-lg-6 form-group">
                            <div class="form-group">
                                <label>{{ __("Maximum") }}</label>
                                <div class="input-group">
                                    <input type="number" class="form--control max-limit" value="0" name="" step="any">
                                    <span class="input-group-text currency"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 mb-10">
            <div class="custom-inner-card">
                <div class="card-inner-header">
                    <h5 class="title">{{ __("Charge") }}*</h5>
                </div>
                <div class="card-inner-body">
                    <div class="row">
                        <div class="col-xxl-12 col-xl-6 col-lg-6 form-group">
                            <div class="form-group">
                                <label>{{ __("Fixed") }}*</label>
                                <div class="input-group">
                                    <input type="number" class="form--control fixed-charge" value="0" name="" step="any">
                                    <span class="input-group-text currency"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12 col-xl-6 col-lg-6 form-group">
                            <div class="form-group">
                                <label>{{ __("Percent") }}*</label>
                                <div class="input-group">
                                    <input type="number" class="form--control percent-charge" value="0" name="" step="any">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4 mb-10">
            <div class="custom-inner-card">
                <div class="card-inner-header">
                    <h5 class="title">{{ __("Rate") }}*</h5>
                </div>
                <div class="card-inner-body">
                    <div class="row">
                        <div class="col-xxl-12 col-xl-6 col-lg-6 form-group">
                            <div class="form-group">
                                <label>{{ __("Rate") }}*</label>
                                <div class="input-group">
                                    <span class="input-group-text append ">1 &nbsp; <span class="default-currency"></span> = </span>
                                    <input type="number" class="form--control rate" value="" name="" step="any">
                                    <span class="input-group-text currency"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12 col-xl-6 col-lg-6 form-group">
                            <div class="form-group">
                                <label>{{ __("Symbol") }}</label>
                                <div class="input-group">
                                    <input type="text" class="form--control symbol" value="" name="" placeholder="{{ __("Symbol") }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>`,
  payment_gateway_currencies_wrapper: `<div class="payment-gateway-currencies-wrapper"></div>`,
  modal_default_alert: `<div class="card modal-alert border-0">
    <div class="card-body">
        <div class="head mb-3">
            {replace}
        </div>
        <div class="foot d-flex align-items-center justify-content-between">
            <button type="button" class="modal-close btn btn--info">{{ __("Close") }}</button>
            <button type="button" class="alert-submit-btn btn btn--danger btn-loading">{{ __("Remove") }}</button>
        </div>
    </div>
  </div>
  `,
  manual_gateway_input_fields:`<div class="row add-row-wrapper align-items-end">
  <div class="col-xl-3 col-lg-3 form-group">
    <label>{{ __("Field Name") }}*</label>
    <input type="text" class="form--control" placeholder="{{ __("Write Here") }}..." name="label[]" value="" required>
  </div>

  <div class="col-xl-2 col-lg-2 form-group">
      <label>{{ __("Field Types") }}*</label>
      <select class="form--control nice-select field-input-type" name="input_type[]">
          <option value="text" selected>{{ __("Input Text") }}</option>
          <option value="file">{{ __("File") }}</option>
          <option value="textarea">{{ __("Textarea") }}</option>
      </select>
  </div>

  <div class="field_type_input col-lg-4 col-xl-4">

  </div>

  <div class="col-xl-2 col-lg-2 form-group">
    <label for="fieldnecessity">{{ __("Field Necessity") }}*</label>
    <div class="toggle-container">
      <div data-clickable="true" class="switch-toggles default two active">
        <input type="hidden" name="field_necessity[]" value="1">
        <span class="switch " data-value="1">{{ __("Required") }}</span>
        <span class="switch " data-value="0">{{ __("Optional") }}</span>
      </div>
    </div>
  </div>

  <div class="col-xl-1 col-lg-1 form-group">
      <button type="button" class="custom-btn btn--base btn--danger row-cross-btn w-100"><i class="las la-times"></i></button>
  </div>
</div>
  `,
  kyc_input_fields:`<div class="row add-row-wrapper align-items-end">
  <div class="col-xl-3 col-lg-3 form-group">
    <label>{{ __("Field Name") }}*</label>
    <input type="text" class="form--control" placeholder="{{ __("Write Here") }}..." name="label[]" value="" required>
  </div>

  <div class="col-xl-2 col-lg-2 form-group">
      <label>{{ __("Field Types") }}*</label>
      <select class="form--control nice-select field-input-type" name="input_type[]">
          <option value="text" selected>{{ __("Input Text") }}</option>
          <option value="file">{{ __("File") }}</option>
          <option value="textarea">{{ __("Textarea") }}</option>
          <option value="select">{{ __("Select") }}</option>
      </select>
  </div>

  <div class="field_type_input col-lg-4 col-xl-4">

  </div>

  <div class="col-xl-2 col-lg-2 form-group">
    <label for="fieldnecessity">{{ __("Field Necessity") }}*</label>
    <div class="toggle-container">
      <div data-clickable="true" class="switch-toggles default two active">
        <input type="hidden" name="field_necessity[]" value="1">
        <span class="switch " data-value="1">{{ __("Required") }}</span>
        <span class="switch " data-value="0">{{ __("Optional") }}</span>
      </div>
    </div>
  </div>

  <div class="col-xl-1 col-lg-1 form-group">
      <button type="button" class="custom-btn btn--base btn--danger row-cross-btn w-100"><i class="las la-times"></i></button>
  </div>
</div>
  `,
  manual_gateway_input_text_validation_field:`<div class="row">
  <div class="col-xl-6 col-lg-6 form-group">
      <label>{{ __("Min Character") }}*</label>
      <input type="number" class="form--control" placeholder="ex: 6" name="min_char[]" value="0" required>
  </div>
  <div class="col-xl-6 col-lg-6 form-group">
      <label>{{ __("Max Character") }}*</label>
      <input type="number" class="form--control" placeholder="ex: 16" name="max_char[]" value="30" required>
  </div>
</div>`,
  manual_gateway_input_file_validation_field: `<div class="row">
  <div class="col-xl-6 col-lg-6 form-group">
    <label>{{ __("Max File Size (mb)") }}*</label>
    <input type="number" class="form--control" placeholder="ex: 10" name="file_max_size[]" value="10" required>
  </div>
  <div class="col-xl-6 col-lg-6 form-group">
    <label>{{ __("File Extension") }}*</label>
    <input type="text" class="form--control" placeholder="ex: jpg, png, pdf" name="file_extensions[]" value="" required>
  </div>
</div>`,
manual_gateway_select_validation_field: `<div class="row">
<div class="col-xl-12 col-lg-12 form-group">
  <label>{{ __("Options") }}*</label>
  <input type="text" class="form--control" placeholder="Type Here..." name="select_options[]" required>
</div>
</div>`,
setup_section_footer_social_link_input:`
<div class="row align-items-end">
  <div class="col-xl-3 col-lg-3 form-group">
      <label>{{ __("Icon") }}*</label>
      <input type="text" class="form--control icp icp-auto iconpicker-element iconpicker-input" placeholder="{{ __("Write Here") }}..." name="icon[]">
  </div>
  <div class="col-xl-8 col-lg-8 form-group">
      <label>{{ __("Link") }}*</label>
      <input type="text" class="form--control" placeholder="{{ __("Write Here") }}..." name="link[]">
  </div>
  <div class="col-xl-1 col-lg-1 form-group">
      <button type="button" class="custom-btn btn--base btn--danger row-cross-btn w-100"><i class="las la-times"></i></button>
  </div>
</div>
`,
};

function replaceText(htmlMarkup,updateText) {
  return htmlMarkup.replace("{replace}",updateText);
}

function getHtmlMarkup() {
  return storedHtmlMarkup;
}

$('.input-field-generator').on('click', '.add-row-btn', function() {
  var source = $('.input-field-generator').attr("data-source");
  $(this).closest('.input-field-generator').find('.results').children().removeClass("last-add");
  $(this).closest('.input-field-generator').find('.results').prepend(storedHtmlMarkup[source]);
  var lastAddedElement = $(this).closest('.input-field-generator').find('.results').children().first();
  lastAddedElement.addClass("last-add");

  var inputTypeField = lastAddedElement.find(".field-input-type");
  if(inputTypeField.length > 0) {
    inputFieldValidationRuleFieldsShow(inputTypeField);
  }
});

$(document).ready(function(){
  var elements = $(".add-row-btn").closest('.input-field-generator').find('.results').children();
  $.each(elements,function(index,item) {
      if($(item).find(".field-input-type").length > 0) {
          inputFieldValidationRuleFieldsShow($(item).find(".field-input-type"));
      }
  });
});

$(document).on("change",".field-input-type",function(){
  inputFieldValidationRuleFieldsShow($(this));
});

function inputFieldValidationRuleFieldsShow(element) {
    if($(element).attr("data-show-db") != undefined) {
        element.removeAttr("data-show-db");
        return false;
    }
  var value = element.val();
  var validationFieldsPlaceElement = $(element).parents(".add-row-wrapper").find(".field_type_input");
  if(value == "text" || value == "textarea") {
    var textValidationFields = getHtmlMarkup().manual_gateway_input_text_validation_field;
    validationFieldsPlaceElement.html(textValidationFields);
  }else if(value == "file") {
    var textValidationFields = getHtmlMarkup().manual_gateway_input_file_validation_field;
    validationFieldsPlaceElement.html(textValidationFields);
    var select2Input = validationFieldsPlaceElement.find(".select2-auto-tokenize");
    $(select2Input).select2();
  }else if(value == "select") {
    var textValidationFields = getHtmlMarkup().manual_gateway_select_validation_field;
    validationFieldsPlaceElement.html(textValidationFields);
  }
  // Refresh all file extension input name
  var fileExtenionSelect = $(element).parents(".results").find(".add-row-wrapper").find(".file-ext-select");
  $.each(fileExtenionSelect,function(index,item) {
    var fileExtSelectFieldName = "file_extensions["+index+"][]";
    $(item).attr("name",fileExtSelectFieldName);
  });
}

 function openDeleteModal(URL,target,message,actionBtnText = "{{ __('Remove') }}",method = "DELETE"){
    if(URL == "" || target == "") {
        return false;
    }

    if(message == "") {
        message = "{{ __('Are you sure to delete ?') }}";
    }
    var method = `<input type="hidden" name="_method" value="${method}">`;
    openModalByContent(
        {
            content: `<div class="card modal-alert border-0">
                        <div class="card-body">
                            <form method="POST" action="${URL}">
                                <input type="hidden" name="_token" value="${laravelCsrf()}">
                                ${method}
                                <div class="head mb-3">
                                    {{ __('${message}') }}
                                    <input type="hidden" name="target" value="${target}">
                                </div>
                                <div class="foot d-flex align-items-center justify-content-between">
                                    <button type="button" class="modal-close btn btn--info">{{ __("Close") }}</button>
                                    <button type="submit" class="alert-submit-btn btn btn--danger btn-loading">${actionBtnText}</button>
                                </div>
                            </form>
                        </div>
                    </div>`,
        },

    );
}

function openModalByContent(data = {
    content:"",
    animation: "mfp-move-horizontal",
    size: "medium",
    }) {
    $.magnificPopup.open({
        removalDelay: 500,
        items: {
        src: `<div class="white-popup mfp-with-anim ${data.size ?? "medium"}">${data.content}</div>`, // can be a HTML string, jQuery object, or CSS selector
        },
        callbacks: {
        beforeOpen: function() {
            this.st.mainClass = data.animation ?? "mfp-move-horizontal";
        },
        open: function() {
            var modalCloseBtn = this.contentContainer.find(".modal-close");
            $(modalCloseBtn).click(function() {
            $.magnificPopup.close();
            });
        },
        },
        midClick: true,
    });
}

function laravelCsrf() {
    return $("head meta[name=csrf-token]").attr("content");
}
</script>
