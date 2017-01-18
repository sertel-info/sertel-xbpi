


	 function verificaDDR(){
		    if ($('#ddr').val() == ''){
		         $("#ddr").attr('disabled', 'disabled');
		         $('#habDDR').bootstrapSwitch('state', false);
		       } else {
		         $("#ddr").removeAttr('disabled');  
		         $('#habDDR').bootstrapSwitch('state', true);
		    }
	 }
     
     function setTec(){
           var val = $('#tec').val(); 
	       //console.log('setTec()');


           if( [12,13,14].indexOf(val) != -1 ){
                $('.nat').show();
                $("#centrais").hide();
         
           }
           else if(val==15){
           		console.log("fxs");
           		
           		$.get("./ramais/getFxsUsedPorts", {} , function(resp){
           			var ports = JSON.parse(resp);

           			for(var i = 0; i< ports.length; i++){
           				 var el = $('.porta').find('input[type=radio][value='+ports[i]+']');
           				 if(!el.prop("checked")){
           						   el.prop('disabled', true);
           				 }
           			}
           		});

           		$('.porta').show();
           		$("#centrais").hide();
           }
           else if(val==16){
           		$("#centrais").show();
           }
           else {
           	    $('.nat').hide();
           	    $("#centrais").hide();
           	    $('.porta').hide();
           }
     }

	 function setType(){
	                    //console.log("setType");
	                    $('.form_pabx').hide();
	                    $('.form_din').hide();           
	                    var $tec = $('#tec');
	                    var $app = $('#app');
	                    $tec.removeAttr('disabled');

	                    if ($app.parent().hasClass('has-error') ){
	                      $app.parent().removeClass("has-error");
                        }

	                    if($('#tipo').val()==1 || $('#tipo').val()==0){
	                       
	                       var query = [];
	                       var opt_to_hide=[15, 16, 112, 113, 114, 115, 116];

	                       for(var i =0; i< opt_to_hide.length; i++){
	                       		query.push('option[value='+opt_to_hide[i]+']');
	                       }

	                       $(query.join(",")).hide();
	                       
	                       $app.val('111');
	                       $app.attr('disabled', 'disabled');
	                       $('.form_trc').hide();
	                       $('.profile').hide();
	                       $('.porta').hide();
	                    
	                    } else if ($('#tipo').val() == 39) {
	                        var query = [];
	                        var opt_to_show=[15, 16, 112, 113, 114, 115, 116];

	                        for(var i = 0; i < opt_to_show.length ; i++){
	                       		query.push('option[value='+opt_to_show[i]+']');
	                        }

	                        $(query.join(',')).show();

	                        mostrarFormApp(); 
	                        $app.removeAttr('disabled');
	                        $('.form_trc').hide();
     	                    $('.profile').show();
	                    } 
	 }
	    	    
	 function setDeviation(){
	           $('.form_deviation').hide();
	           if($('#deviation').val()!=0){
	           $('.form_deviation').show();
	           } 
	 }

	 function setDesvioPara(){
	           //console.log('setDesvioPara()');
	           if($('#detour').val() != 0){

	               if($('#detour').val() == 3){
	                   $('.noexterno').show();   
	               } else {
	                   $('.noexterno').hide();
	               }
	             
	           } else {
	              $('.noexterno').hide();
	           }
	 }
	     
	 function setDesvio(){
	            //console.log("setDesvio()");
	            var desvio = $('#deviation').val();

	            if (desvio != 0){ 
	              $('.detour').show();
	            } else if (desvio == 0 ){
	              $('.detour').hide();
	            }
	 }

	 function mostrarFormApp(){
	             //console.log('mostrarFormApp()');
	             var opt_to_show = [];
	             var opt_to_hide = [];

	              if($('#app').val() != 0){
	                 
		                if($("#habDDR").val('') ){
		                  		 ($("#ddr").attr('disabled','disabled'));
		                } else{
		                   		 ($("#ddr").removeAttr('disabled'));
		                }

		                if($('#app').val()==111){ //PABX
			                  opt_to_show.push('.form_din', '.form_pabx', '#habDDR');
			                  opt_to_hide.push('#file');
			                  
			                  $('#tec').removeAttr('disabled');

			                  if($('#tipo').val() == 39) {
			                  	 opt_to_show.push('.profile');
			                  } 
			                  else{
			                  	 opt_to_hide.push('.profile');
			                  }

		                } else if($('#app').val()==112){ //DAC
			                  opt_to_show.push('.form_din', '.fila');
			                  opt_to_hide.push('.form_pabx', '.profile', '.nat');
			                  
			                  $('#tec').removeAttr('disabled');
		                
		                } else if($('#app').val()==113){// URA
			                  opt_to_show.push('.form_din');
			                  opt_to_hide.push('.form_pabx', '#fila', '.profile', '.nat');
			                  
			                  $('#tec').removeAttr('disabled');
		                
		                } else if($('#app').val()==116){ //Porteiro
			                  opt_to_show.push('.nat');
			                  opt_to_hide.push('.form_pabx');

			                  $('#tec').val('12');
			                  $('#tec').attr('disabled', 'disabled');	
		                }
		                else {
			                  opt_to_show.push('.form_din');
			                  opt_to_hide.push('.form_pabx', '#fila', '.profile', '.nat');
			                 
		                      $('#tec').removeAttr('disabled');
		                } 
	               }
	               else {
	                  opt_to_hide.push('.form_din', '#fila', '.profile', '.nome', '.habDDR', '.form_pabx', '.nat');

	                  $('#tec').removeAttr('disabled');
	                  $('#tec').val('0');
	              
	               }

	               $(opt_to_show.join(',')).show();
	               $(opt_to_hide.join(',')).hide();

	 } 
	 
	 function resetaCheckBoxes (){
	       var checkbox_to_reset = ['habDDR',
									'notdisturb',
									'conference',
									'accountcode',
									'centercost',
									'intercomaccess',
									'capture',
									'parking_calls',
									'nat',
									];

		  var query = [];
		  for (var i = 0; i < checkbox_to_reset.length ; i++){
		  	 	query.push('input[name='+checkbox_to_reset[i]+']');
		  }

		  $(query.join(',')).bootstrapSwitch('state', false);
	      
	 }
	       


     
 
