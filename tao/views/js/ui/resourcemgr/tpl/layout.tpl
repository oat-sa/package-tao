<div class="resourcemgr modal">


<!-- left section: items selection -->
    <section class="file-browser">
        
        <h1>{{ __ 'Browse resources'}}</h1>
        <ul class="folders"></ul>
    </section>
 
<!-- test editor  -->
    <section class="file-selector">
        <h1>
            <div class="title"></div> 
            <div class="upload-switcher">
                <a href="#"><span class="icon-add"></span>{{__ 'Upload'}}</a>
            </div>
        </h1>
        
        <div class="empty">
            {{__ 'No files'}}
        </div>

        <ul class="files"></ul>

        
        <form class="uploader">            
            <div class="grid-row">
                <p>{{__ 'Upload to'}} : <span class="current-path"></span></p>
            </div>
            <div class="file-upload grid-row">
                <span class="btn-info btn-browse small col-4"></span>
                <span class="file-name col-8 truncate"></span>
                <input type="file" name="content">
            </div>
            <div class="grid-row"> 
                <div class="file-drop col-12">
                    - {{__ 'or'}} -<br />
                    {{__ 'Drop file here'}}
                </div>
            </div>
            <div class="grid-row">
                <div class="progressbar"></div>
                <br />
            </div>
            <div class="grid-row"> 
                <button class="btn-success btn-upload small"></button>
            </div>
        </form>
    </section>   

    <section class="file-preview">
        <h1>{{__ 'Preview'}}</h1>
        <div class="previewer">
            <p class="nopreview">{{__ 'No Preview available'}}</p>
        </div>
       
        <h2 class="toggler" data-toggle="~ .file-properties">{{__ 'File Properties'}}</h2>
        <div class="file-properties">

            <div class="grid-row">
                <div class="col-2">
                    {{__ 'Type'}}
                </div>
                <div class="col-10 prop-type"></div>
            </div>

            <div class="grid-row">
                <div class="col-2">
                    {{__ 'Size'}}
                </div>
                <div class="col-10 prop-size"></div>
            </div>
            
            <div class="grid-row">
                <div class="col-2">
                    {{__ 'URL'}}
                </div>
                <div class="col-10 prop-url">
                    <a href="#" target="_blank"></a>
                </div>
            </div>
        </div>

        <h2 class="toggler" data-toggle="~ .actions">{{__ 'Actions'}}</h2>
        <div class="actions">
            <button class="btn-success select-action" disabled>
                <span class="icon-move-item"></span>{{__ 'Select'}}
            </button>
        </div>
    </section>
</div>
