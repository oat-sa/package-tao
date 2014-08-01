<div class="resourcemgr modal">

    <h2>{{title}}</h2>
    
<!-- left section: items selection -->
    <section class="file-browser">
        
        <h1>{{ __ 'Browse resources'}}</h1>
        <ul class="folders"></ul>
    </section>
 
<!-- test editor  -->
    <section class="file-selector">
        <h1>
            <div class="title lft"></div>
            <div class="upload-switcher rgt">
                <a href="#" class="btn-info small upload"><span class="icon-add"></span>{{__ 'Add file(s)'}}</a>
                <a href="#" class="btn-info small listing"><span class="icon-undo"></span>{{__ 'Back to listing'}}</a>
            </div>
        </h1>
        
        <div class="empty">
            {{__ 'No files'}}
        </div>

        <ul class="files"></ul>

        <div class="file-upload-container"></div>
    
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
            <button class="btn-success select-action small" disabled>
                <span class="icon-move-item"></span>{{__ 'Select'}}
            </button>
        </div>
    </section>
</div>
