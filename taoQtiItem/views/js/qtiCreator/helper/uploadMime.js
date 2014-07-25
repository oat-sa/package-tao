define([
    'i18n',
], function(__){
    
    var uploadMime = {
        getMimeTypes : function() {
            return [
                { "mime" : "application/zip", "label" : __("ZIP file") },
                { "mime" : "text/plain", "label" : __("Plain text") },
                { "mime" : "application/pdf", "label" : __("PDF file") },
                { "mime" : "image/jpeg", "label" : __("JPEG image") },
                { "mime" : "image/png", "label" : __("PNG image") },
                { "mime" : "image/gif", "label" : __("GIF image") },
                { "mime" : "image/svg+xml", "label" : __("SVG image") },
                { "mime" : "application/ogg", "label" : __("audio/mpeg") },
                { "mime" : "audio/x-ms-wma", "label" : __("Windows Media audio") },
                { "mime" : "audio/x-wav", "label" : __("WAV audio") },
                { "mime" : "video/mpeg", "label" : __("MPEG video") },
                { "mime" : "video/mp4", "label" : __("MP4 video") },
                { "mime" : "video/quicktime", "label" : __("Quicktime video") },
                { "mime" : "video/x-ms-wmv", "label" : __("Windows Media video") },
                { "mime" : "video/x-flv", "label" : __("Flash video") },
                { "mime" : "text/csv", "label" : __("CSV file") },
                { "mime" : "application/msword", "label" : __("Microsoft Word file") },
                { "mime" : "application/vnd.ms-excel", "label" : __("Microsoft Excel file") },
                { "mime" : "application/vnd.ms-powerpoint", "label" : __("Microsoft Powerpoint file") }
            ]
        }
    }

    return uploadMime;
});