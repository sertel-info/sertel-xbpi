
<div class='container'>
	<div class='row'>
	<h3> Filtar <span style='' class='glyphicon glyphicon-search' aria-hidden="true"> </span> </h3> 
	<br>
		<form id='form_filtro' method='GET'>
			<div class='form-group col-md-12'>
				<div class='col-md-5'>
				 <center>
					<div class='row' style=''>
						<div class='form-group form-group-sm'>
							<div style="float:left">
								<label> Num. Origem:  </label> <input style='width:80%' name='filtro_orig' placeholder='(21)3333-4444' type='text' class='form-control'/>
							</div>

							<div style="float:left">
								<label> Num. Destino: </label> <input style='width:80%' name='filtro_dest' placeholder='(21)3333-4444' type='text' class='form-control'/> 
							</div>
						</div>
					</div>
				
					
					</center>

					<div class='form-group' style='margin-top:15px'> 
						<div style='display:block'>
								<label> Possui o Comentário: </label> 
								<input style='width:100%' type='text' style='' name='filtro_comentario' placeholder='Comentário' class='form-control'/>
						</div>
					</div>
				</div>
                <div class='col-md-5'>
					<div class='row'>
						<div class='form-group form-group-sm'>
								
							<div style="float:left">
								<center>
									<label> Entre a hora : </label>
									<input style='width:50%' name='filtro_hora_ini' type='text' placeholder='00:00' class='form-control'/> 
								</center>
								</div>
							<div style="float:left">
								<label> E a hora: </label> 
								<input style='width:50%' name='filtro_hora_fim' type='text' placeholder='12:00' class='form-control'/>
							</div>
						</div>
					</div>
					<div class='row' style='margin-top:15px'>
						<div class='form-group form-group-sm'>
							
							
							<div style="float:left">
								<center>
								<label> Entre o dia: </label>
								<input style='width:50%' type='text' name='filtro_data_ini' placeholder='01/01/2001' class='form-control'/> 
								</center>
							</div>

							<div style="float:left">
								<label> E o dia: </label> 
								<input style='width:50%' type='text' name='filtro_data_fim' placeholder='01/01/2001' class='form-control'/>
							</div>
						</div>
					</div>
					<div class='row' style='margin-top:15px'>
						<div class='form-group form-group-sm'>
							<div style="float:left">
								<center>
									<label> Durou entre: </label>
									<input type='text' style='width:50%' name='filtro_duracao_ini'  placeholder='00:00' class='form-control'/> 
								</center>
							</div>
							<div style="float:left">
								<label> E: </label>
								<input type='text' style='width:50%' name='filtro_duracao_fim'  placeholder='00:00' class='form-control'/> 
							</div>
						</div>						
					</div>

			</div>
			


			<div style='margin-left:15px; float:left' class='col-md-4'>	
				<button style='width:auto' type='submit' id='filtrar' class='form-control btn btn-primary'>Filtrar <span style='' class='glyphicon glyphicon-search' aria-hidden="true"> </span>
				</button>	
			</div>
		</form>
	</div>
</div>
<br>


@push('scripts')
<script type="text/javascript" src="/assets/js/jquery.maskedinput.min.js"></script>

<script type="text/javascript">
	        $(function(){
	        	$("input[name=com_tempo]").mask("99:99:99");
	        	$("input[name=filtro_hora_ini]").mask("99:99");
	        	$("input[name=filtro_hora_fim]").mask("99:99");
	        	$("input[name=filtro_data_ini]").mask("99/99/9999");
	        	$("input[name=filtro_data_fim]").mask("99/99/9999");
	        	$("input[name=filtro_duracao_ini]").mask("99:99");
	        	$("input[name=filtro_duracao_fim]").mask("99:99");
	        });
</script>
@endpush