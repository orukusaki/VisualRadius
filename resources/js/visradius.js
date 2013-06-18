$('.modalform').submit(function () {
  var formdata = $(this).serializeArray();
  var save = false;

  $(formdata).each(
      function (idx, item) {
        console.log($('#seekrit-form input[name="'+item.name+'"]'));
      if ($('#seekrit-form input[name="'+item.name+'"]').length) {
        $('#seekrit-form input[name="'+item.name+'"]').val(item.value);
      } else {
        $('#seekrit-form').append($('<input type="hidden" name="'+item.name+'" value="'+item.value+'" />'));
      }
      if (item.name == "save") {
        save = item.value;
      }
    }
  );

  if (save == true) {
    return true;
  }
  // Seekrit form always saves
  $('#seekrit-form input[name="save"]').val(true);

  formdata.push({"name": "base64", "value": true});

  $.ajax({
      type: $(this).prop("method"),
      url : $(this).prop("action"),
      data: formdata,
      success : function (response) {

          $('#myModal img').prop('src', 'data:image/png;base64,'+ response);
          $('#myModal').modal('show');
      }
  });
 return false;
});
