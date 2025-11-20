jQuery(document).ready(function ($) {
  $(".acf-field").each(function () {
    var field = $(this);
    var tooltipIcon = field.find(".sb-acf-tooltip");
    if (tooltipIcon.length) {
      // Move tooltip after the label
      field.find("label").append(tooltipIcon);
    }
  });
});
