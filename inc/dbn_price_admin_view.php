<div class="wrap">

	<style>
	#wpcontent{background-color:#fff;}
	.last_ten_records td,.last_ten_records th{
	    font-size:1.2em;
	    text-align:left;
	    padding: 5px 15px;
	}
	.last_ten_records tr:nth-of-type(even){
	    background-color:#e0e0e0;
	}
	div#ui-datepicker-div 
	{
		background: rgba(255,255,255,0.9);
		box-shadow: 1px 1px 1px rgba(0,0,0,.4);
		padding:10px;
	}
	span.ui-icon.ui-icon-circle-triangle-w { margin-right: 15px; }
	td a.ui-state-default.ui-state-hover {
    	background: #fff;
		box-shadow: 0 0 1px rgba(0,0,0,.2);
	}
	.ui-datepicker-title {
    	text-align: center;
    	font-weight: 700;
	}
		th[scope=row]{
			text-align:right;
		}
		.error{
			display:table-cell;
			color:red;
		}
	</style>
	
    <h1> Share Price </h1><hr>
	<p><br>
		To get latest stock price data <b>open, close, highest, lowest and volume</b><br>
		e.g to get most recent open price use<code>[dbn_share_price get="open"] </code> ?>
	</p>

    <hr>
 
    <h3>Record in the last 30 days</h3>

    <table class='last_ten_records'>
        <thead><th>Date</th> <th>Open Price</th> <th>High Price</th> <th>Low Price</th> <th>Close Price</th> <th>Volume</th></thead>
        <tbody>
            <?php 
                
                do_shortcode("[dbn_admin_records]");
                    
            ?>
        </tbody>
           
    </table>
	<script>
		
	</script>
</div>