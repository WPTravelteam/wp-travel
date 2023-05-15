
class WpTravelSEOAnalysisEditorText {
    static onChangeAnalysisData = '';
    constructor() {
       if ( typeof YoastSEO === "undefined" || typeof YoastSEO.analysis === "undefined" || typeof YoastSEO.analysis.worker === "undefined" ) {
            return;
        }
        YoastSEO.app.registerPlugin( "WpTravelSEOAnalysisEditorText", { status: "ready" } );
        this.registerModifications();
        // console.log( 'dsjfh', this.onChangeAnalysisData );
    }
    /**
     * 
     * @param {trip edit data } allData 
     * @override old data to new editor data
     */
    static changeAnalysisTextData( allData ) {
        // console.log( 'allData', allData );
        const  { trip_overview, trip_outline, trip_include, trip_exclude, advanced_gallery, gallery
        } = typeof allData != undefined && allData;
        const trp_overview = typeof trip_overview != 'undefined' && trip_overview || '';
        const trp_outline = typeof trip_outline != 'undefined' && trip_outline || '';
        const trp_include = typeof trip_include != 'undefined' && trip_include || '';
        const trp_exclude = typeof trip_exclude != 'undefined' && trip_exclude || '';
        const { items }      = typeof  advanced_gallery != 'undefined' && advanced_gallery || [];
        const g_url = typeof items != 'undefined' && items.length > 0 && items.map( gallerys => {return gallerys.url } ) || [];
        var img_tag_string = '';
        typeof g_url != 'undefined' && Array.isArray( g_url ) && g_url.length > 0 && g_url.forEach( url_item => img_tag_string += "<img src='" + url_item + "' alt='gallery' />" );
        typeof gallery != 'undefined' && Array.isArray( gallery ) && gallery.length > 0 && gallery.forEach( gallery_item => img_tag_string += "<img src='" + gallery_item.thumbnail + "' alt='gallery' />" );
        // console.log( 'img_tag_string', img_tag_string );
        const combineData = trp_overview + trp_outline + trp_include + trp_exclude + img_tag_string;
    //    console.log( 'combineData', combineData );
        WpTravelSEOAnalysisEditorText.onChangeAnalysisData = combineData;
    }
    /**
     * Registers the addContent modification.
     *
     * @returns {void}
     */
    registerModifications( ) {
        const callback = this.addContent.bind( this );

        // Ensure that the additional data is being seen as a modification to the content.
        YoastSEO.app.registerModification( "content", callback, "WpTravelSEOAnalysisEditorText", 10 );
    }

    /**
     * Adds to the content to be analyzed by the analyzer.
     *
     * @param {string} data The current data string.
     *
     * @returns {string} The data string parameter with the added content.
     */
    addContent( data ) {
        data = WpTravelSEOAnalysisEditorText.onChangeAnalysisData

        return data ;
    }
}

export default WpTravelSEOAnalysisEditorText;

