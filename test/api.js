$(function(){
	$('form').submit(function(event){
    console.log('click btn');
		event.preventDefault();
    //リクエストの下準備
    //リクエスト時に一緒に送るデータの作成
    var $form = $('#my-form');
    var query = $form.serialize();
		var param = $form.serializeArray();
		console.log(query);
    console.log(param);
		var send_data;
		send_data = {
			rpc : param[0]["value"],
			tfa : param[1]["value"],
		};
		console.log(send_data);
    //WebAPIを叩く
    $.ajax({
      //リクエストの内容
			//type:'POST',
      url: 'http://seeb.jp/apitest/apitest.php',
      data: send_data,
      timeout: 10000,
      dataType: "json",
    })
    .done(function( data ) {
        // ...
				console.log( 'done' );
				$('div[data-result=""]').html(JSON.stringify(data));

				//dataがサーバから受け取るjson値
				var rows = "";
				$('#tbl tr').remove();
        for (i = 1; i<Object.keys(data).length+1; i++) {
            rows += "<tr>";
						rows += '<td bgcolor="#ffffff">';
            rows += i;
            rows += "</td>";
            rows += "<td>";
            rows += data[i].toFixed(4);
            rows += "</td>";
            rows += "</tr>";
        }
        $('#tbl').append(rows);

				return false;
		})
		.fail(function( data ) {
        // ...
				console.log( 'fail' );
				$('div[data-result=""]').html(JSON.stringify(data));
		})
		.always(function( data ) {
        // ...
				console.log( data );
		});
  });
});
