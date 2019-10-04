/**
 * Drip eCRM Connector plugin for Craft CMS
 *
 * Settings Field JS
 *
 * @author    Extreme
 * @copyright Copyright (c) 2019 Extreme
 * @link      madebyextreme.com
 * @package   Drip
 * @since     1.0.0
 */


$('.lightswitch').on('change', function(e){
  $(e.currentTarget).closest('.lightswitch-field').next('.b-settings-reveal').toggleClass('active');
});

$('#api-test').on('click', function(e) {
  e.preventDefault();
  $.ajax({
    type: 'get',
    url: '/actions/drip/settings/get-fields',
    success: function (response) {
      console.log(response);
      if (response.fields.errors){
        alert(response.fields.errors[0].message);
      } else {
        alert('API connection ok');
      }
    },
  });
});

$('#field-refresh').on('click', function(e) {
  e.preventDefault();
  $.ajax({
    type: 'get',
    url: '/actions/drip/settings/get-fields',
    success: function (response) {
      console.log(response);
      if (response.fields.errors){
        alert(response.fields.errors[0].message);
      } else {
        window.location.reload();
      }
    },
  });
});
