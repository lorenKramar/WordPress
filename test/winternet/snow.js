var GUID = function() {
    var S4 = function ()
    {
        return Math.floor(
                Math.random() * 0x10000 /* 65536 */
            ).toString(16);
    };

    return (
            S4() + S4() + "-" +
            S4() + "-" +
            S4() + "-" +
            S4() + "-" +
            S4() + S4() + S4()
        );
};

$(function(){
	// <div class="flakebox" style="width:random"><input type="radio" for="random" /></div>


	var numFlakes=45;

	$('.snow_box').each(function(i){
		for(var i=0; i<numFlakes; i++){

			var radioButton = $('<input type="radio" />').attr('for', 'p' + GUID()).attr('checked', false),
				flakebox = $('<div class="flakebox">');

			radioButton[0].style.MozAnimationDelay = (Math.random() * 2) * -1000 + 'ms';
			flakebox[0].style.MozAnimationDelay = (Math.random() * 20) * -1000 + 'ms';

			

			flakebox[0].style.MozAnimationDuration = Math.round((Math.floor(Math.random() * 17) + 11) * 1000) + 'ms'

			console.log(flakebox[0].style)
			$(this).append(
				flakebox.css({
					
					left:Math.random() * 100 + '%',
					// width: (Math.floor(Math.random() * 50) + 20) + 'px'
				}).append(
					radioButton
				)
			)
		}

	});
});