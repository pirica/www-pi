
<script src="scripts/jquery/jquery.ui.widget.js"></script>
<script src="scripts/jquery/jquery.iframe-transport.js"></script>
<script src="scripts/jquery/jquery.fileupload.js"></script>
<script src="scripts/jquery/jquery.fileupload-process.js"></script>
<script src="scripts/jquery/jquery.fileupload-image.js"></script>
<script src="scripts/jquery/jquery.fileupload-audio.js"></script>
<script src="scripts/jquery/jquery.fileupload-video.js"></script>
<script src="scripts/jquery/jquery.fileupload-validate.js"></script>
<script src="scripts/jquery/jquery.fileupload-ui.js"></script>

<h1>Upload files</h1>
<?php  // https://blueimp.github.io/jQuery-File-Upload/index.html ?>

<div class="container">
    
    <form id="fileupload" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
       <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
				
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start upload</span>
                </button>
				
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel upload</span>
                </button>
				
                <button type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
				
                <input type="checkbox" class="toggle">
                
				
                <span class="fileupload-process"></span>
            </div>
			
            <div class="col-lg-5 fileupload-progress fade">
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
				
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
		
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
		
    </form>
   
</div>
