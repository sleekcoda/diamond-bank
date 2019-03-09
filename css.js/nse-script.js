(function($) {
	$.get('https://marketdataapi.nse.com.ng:8447/v2/api/quote/stockquotes.json?s=DIAMONDBNK&_t=ff124bb2-2db6-4cfb-b3cc-8315c98ba4b8', function( data ) {
        var weekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        var monthNames = ["January", "February", "March","April", "May", "June", "July","August", "September", "October","November", "December"];

        let ref_container = $('#ir-stock-display');
        res = data[0];
        //Set closing price
        $('.ir-stock-price > span').html(res.PrevClose+res.Currency);
        //Set volume traded
        $('.ir-stock-volume').html(res.Volume.toLocaleString());
        //Set date
        m = new Date(res.TradeDate);
        $('.ir-stock-date').html( weekday[m.getDay()]+', '+monthNames[m.getUTCMonth()]+' '+(m.getUTCDate()+1)+', '+m.getUTCFullYear() );

    })
	
})( jQuery );