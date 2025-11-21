jQuery(document).ready(function ($) {
  $(".acf-field").each(function () {
    var field = $(this);
    var tooltipIcon = field.find(".sb-acf-tooltip");
    if (tooltipIcon.length) {
      // Move tooltip after the label
      field.find("label").append(tooltipIcon);
    }
  });

  function toggleWidth() {
    var toggle = $(".acf-field-setting-open_as_modal input");
    var width = $(".acf-field-setting-tooltip_width");

    // Check if toggle is checked
    if (toggle.is(':checked')) {
        width.hide(); // Hide if checked
    } else {
        width.show(); // Show if not checked
    }
  }
  toggleWidth();

  // On toggle change call toggleWidth
  $(".acf-field-setting-open_as_modal input").on('change', function() {
    toggleWidth();
  });
  
  // Function to close modal
  function closeModal() {
    $('.sb-acf-tooltip.active').removeClass('active');
    $('.sb-acf-tooltip-overlay').remove();
    $('body').removeClass('sb-tooltip-modal-open');
  }
  
  // Handle tooltip clicks for modal mode
  $(document).on('click', '.sb-acf-tooltip', function(e) {
    var $tooltip = $(this);
    var isModal = $tooltip.data('is-modal') == 1;
    
    if (isModal) {
      e.stopPropagation();
      
      // Toggle the active class
      if ($tooltip.hasClass('active')) {
        closeModal();
      } else {
        // Close any other open tooltips
        closeModal();
        
        // Add overlay
        $('body').append('<div class="sb-acf-tooltip-overlay"></div>');
        $('body').addClass('sb-tooltip-modal-open');
        
        // Open this tooltip
        $tooltip.addClass('active');
      }
    }
  });
  
  // Close modal on overlay click
  $(document).on('click', '.sb-acf-tooltip-overlay', function() {
    closeModal();
  });
  
  // Close button click (using event delegation on the ::before pseudo-element area)
  $(document).on('click', '.sb-acf-tooltip[data-is-modal="1"].active .content-wrapper', function(e) {
    // Check if click is in the top-right area (close button)
    var $wrapper = $(this);
    var offset = $wrapper.offset();
    var clickX = e.pageX - offset.left;
    var clickY = e.pageY - offset.top;
    var width = $wrapper.outerWidth();
    
    // If click is in top-right corner (close button area)
    if (clickX > width - 40 && clickY < 40) {
      e.stopPropagation();
      closeModal();
    }
  });
  
  // Close modal on escape key
  $(document).on('keydown', function(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      closeModal();
    }
  });
  
  // Prevent clicks inside tooltip from closing it
  $(document).on('click', '.sb-acf-tooltip-inner', function(e) {
    e.stopPropagation();
  });
});