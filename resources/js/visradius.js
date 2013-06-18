$('.modalform').submit(function () {
  var formdata = $(this).serializeArray();
  var save = false;

  $(formdata).each(
      function (idx, item) {
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
  $('#myModal').modal('show');

  $.ajax({
      type: $(this).prop("method"),
      url : $(this).prop("action"),
      data: formdata,
      beforeSend: function (jqXHR) {
        jqXHR.overrideMimeType('text/plain; charset=x-user-defined');
      },
      dataFilter : function (response, dataType) {

        // Convert silly unicode back to a nice byte stream.
        var bytes = new Array(response.length);
        for (i = 0; i <= response.length; i++ ) {
          bytes[i] = String.fromCharCode(response.charCodeAt(i) & 0x00ff);
        }

        return bytes.join('');
      },
      success : function (response, status, xhr) {
        $('#myModal img').prop('src', 'data:image/png;base64,' + btoa(response));
      }
  });
 return false;
});
