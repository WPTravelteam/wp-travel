
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
        const  { trip_overview, trip_outline, trip_include, trip_exclude } = typeof allData != undefined && allData;
        const trp_overview = typeof trip_overview != 'undefined' && trip_overview || '';
        const trp_outline = typeof trip_outline != 'undefined' && trip_outline || '';
        const trp_include = typeof trip_include != 'undefined' && trip_include || '';
        const trp_exclude = typeof trip_exclude != 'undefined' && trip_exclude || '';
        const combineData = trp_overview + trp_outline + trp_include + trp_exclude;
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

