$(document).ready(function(){
  // Add user
  $(document).on('click', '.good_add', function(){
    //user Info
    var good_id = $('#good_id').val();
    var good = $('#good').val();
    var number = $('#number').val();
    var origin = $('#origin').val();
    //Additional Info
    var dev_uid = $('#dev_uid').val();
    var fragile = $(".fragile:checked").val();
    var dev_uid = $('#dev_sel option:selected').val();
    
    $.ajax({
      url: 'manage_goods_conf.php',
      type: 'POST',
      data: {
        'Add': 1,
        'good_id': good_id,
        'good': good,
        'number': number,
        'origin': origin,
        'dev_uid': dev_uid,
        'fragile': fragile,
      },
      success: function(response){

        if (response == 1) {
          $('#good_id').val('');
          $('#good').val('');
          $('#number').val('');
          $('#origin').val('');

          $('#dev_sel').val('0');
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-success">A new Good has been successfully added</p>');
        }
        else{
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-danger">'+ response + '</p>');
        }

        setTimeout(function () {
            $('.alert').fadeOut(500);
        }, 5000);
        
        $.ajax({
          url: "manage_goods_up.php"
          }).done(function(data) {
          $('#manage_goods').html(data);
        });
      }
    });
  });
  // Update user
  $(document).on('click', '.good_upd', function(){
    //user Info
    var good_id = $('#good_id').val();
    var good = $('#good').val();
    var number = $('#number').val();
    var origin = $('#origin').val();
    //Additional Info
    var dev_uid = $('#dev_uid').val();
    var fragile = $(".fragile:checked").val();
    var dev_uid = $('#dev_sel option:selected').val();

    $.ajax({
      url: 'manage_goods_conf.php',
      type: 'POST',
      data: {
        'Update': 1,
        'good_id': good_id,
        'good': good,
        'number': number,
        'origin': origin,
        'dev_uid': dev_uid,
        'fragile': fragile,
      },
      success: function(response){

        if (response == 1) {
          $('#good_id').val('');
          $('#good').val('');
          $('#number').val('');
          $('#origin').val('');

          $('#dev_sel').val('0');
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-success">The selected Good has been updated!</p>');
        }
        else{
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-danger">'+ response + '</p>');
        }
        
        setTimeout(function () {
            $('.alert').fadeOut(500);
        }, 5000);
        
        $.ajax({
          url: "manage_goods_up.php"
          }).done(function(data) {
          $('#manage_goods').html(data);
        });
      }
    });   
  });
  // delete user
  $(document).on('click', '.good_rmo', function(){

    var good_id = $('#good_id').val();

    bootbox.confirm("Do you really want to delete this Good?", function(result) {
      if(result){
        $.ajax({
          url: 'manage_goods_conf.php',
          type: 'POST',
          data: {
            'delete': 1,
            'good_id': good_id,
          },
          success: function(response){

            if (response == 1) {
              $('#good_id').val('');
              $('#good').val('');
              $('#number').val('');
              $('#origin').val('');

              $('#dev_sel').val('0');
              $('.alert_user').fadeIn(500);
              $('.alert_user').html('<p class="alert alert-success">The selected Good has been deleted!</p>');
            }
            else{
              $('.alert_user').fadeIn(500);
              $('.alert_user').html('<p class="alert alert-danger">'+ response + '</p>');
            }
            
            setTimeout(function () {
                $('.alert').fadeOut(500);
            }, 5000);
            
            $.ajax({
              url: "manage_goods_up.php"
              }).done(function(data) {
              $('#manage_goods').html(data);
            });
          }
        });
      }
    });
  });
  // select user
  $(document).on('click', '.select_btn', function(){
    var el = this;
    var card_uid = $(this).attr("id");
    $.ajax({
      url: 'manage_goods_conf.php',
      type: 'GET',
      data: {
      'select': 1,
      'card_uid': card_uid,
      },
      success: function(response){

        $(el).closest('tr').css('background','#70c276');

        $('.alert_user').fadeIn(500);
        $('.alert_user').html('<p class="alert alert-success">The card has been selected!</p>');
        
        setTimeout(function () {
            $('.alert').fadeOut(500);
        }, 5000);

        $.ajax({
          url: "manage_goods_up.php"
          }).done(function(data) {
          $('#manage_goods').html(data);
        });

        console.log(response);

        var good_id = {
          Good_id : []
        };
        var good = {
          Good : []
        };
        var good_on = {
          Good_on : []
        };
        var origin = {
          Origin : []
        };
        var good_dev = {
          Good_dev : []
        };
        var fragile = {
          Fragile : []
        };

        var len = response.length;

        for (var i = 0; i < len; i++) {
            good_id.Good_id.push(response[i].id);
            good.Good.push(response[i].good);
            good_on.Good_on.push(response[i].serialnumber);
            origin.Origin.push(response[i].origin);
            good_dev.Good_dev.push(response[i].device_uid);
            fragile.Fragile.push(response[i].fragile);
        }
        if (good_dev.Good_dev == "All") {
          good_dev.Good_dev = 0;
        }
        $('#good_id').val(good_id.Good_id);
        $('#good').val(good.Good);
        $('#number').val(good_on.Good_on);
        $('#origin').val(origin.Origin);
        $('#dev_sel').val(good_dev.Good_dev);

        if (fragile.Fragile == 'Fragile'){
            $('.form-style-5').find(':radio[good=fragile][value="Fragile"]').prop('checked', true);
        }
        else{
            $('.form-style-5').find(':radio[good=fragile][value="Non Fragile"]').prop('checked', true);
        }

      },
      error : function(data) {
        console.log(data);
      }
    });
  });
});