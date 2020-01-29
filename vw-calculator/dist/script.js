jQuery(document).ready(function() {

	jQuery(".load").hide();
	jQuery(".content").show();


	const serialize = function(e)
	{
		const formData = new FormData(e);
		const data = Array.from(formData.entries()).reduce((memo, pair) => ({
			...memo,
			[pair[0]]: pair[1],
		}), {});
		return data;
	}

	jQuery("button").click(function ()
	{
		jQuery("#submit").val(this.value);
		jQuery('form').submit();
	});


	const send = function(form)
	{

		return $.ajax({
			url : "https://fe4finalproject.000webhostapp.com/live_calculator/calc_request.php",
			data : serialize(form),
			type: "post",
			dataType : "json",
			beforeSend : function()
			{
				jQuery('.output').hide();
				jQuery(".load").show();
				jQuery(".content").hide();
			},
			complete : function()
			{
				jQuery(".load").hide();
				jQuery(".content").show();
			},
			error : function (e) {
				const result = $("<div>").html(e.responseText);
				alert("Validation : " + result.text());
			}
		})

	}


	jQuery('form').submit(function(e) {
		e.preventDefault();
		const request = send(this);
		request.done(function (response) {
			jQuery('.output label').text(response);
			jQuery('.output').show();
		})
	});

});