var makeBox = function(elm, conf){

	var h=(conf.h * 36) + 12,
		w=(conf.w * 36) + 12

	elm.css({
		height:h,
		width:w,
		left:'50%',
		top:'50%',
		marginTop: (h/2) * -1,
		marginLeft: (w/2) * -1
	})

	// <div class="side north"></div>
	var sides = ['north', 'east', 'south', 'west'],
		drawSide=function(side, len){

			var $side =elm.find('.' + side);

			$side.css('width', len * 36);

			for( var i=0; i<len; i++) {
				$side.append(
					'<input type="radio" name="' + side + i + '" />',
					'<input type="radio" name="' + side + i + '" />',
					'<input type="radio" name="' + side + i + '" />');
			}
		};

	elm.append(
		'<div class="side north"></div>\
		<div class="side east"></div>\
		<div class="side south"></div>\
		<div class="side west"></div>');



	$.each(sides, function(i, side){

		if(i%2){
			drawSide(side, conf.h);
		} else {
			drawSide(side, conf.w);
		}

	});

	var lights = elm.find('input[type=radio]'),
		curLight=2;

	setInterval(function(){

		if(curLight===-1) {
			curLight=2;
		}

		for(var i=curLight; i<lights.length; i+=3){
			$(lights[i]).attr('checked', 'checked');
		}

		curLight--;

	}, 200);
	

};