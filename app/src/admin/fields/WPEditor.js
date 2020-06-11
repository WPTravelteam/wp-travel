import { Component } from '@wordpress/element';
// https://wordpress.stackexchange.com/questions/326443/wp-editor-initialize-does-not-show-the-same-default-toolbar
/**
 * Always initiate your react app inside domReady(function () {
 *  redner(...)
 * });
 */
class WPEditor extends Component {
    constructor(props) {
        super(props);
        this.state = {
            editor: null,
            id: props.id
        }

        this.initEditor = this.initEditor.bind(this);
    }


    componentDidMount() {
        this.initEditor();
    }

    componentWillUnmount() {
        tinymce.execCommand('mceRemoveControl', true, `#${this.state.id}`);
        wp.editor.remove(this.state.id);
        tinymce.remove(this.state.editor);
    }

    initEditor(id = null) {
        const $this = this;
        id = null !== this.state.id ? this.state.id : $this.props.id;
        
        if ( typeof wp != 'undefined' &&  typeof wp.editor != 'undefined' ) {
            wp.editor.initialize(`${id}`, {
                tinymce: {
                    wpautop: true,
                    plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                    toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | spellchecker',
                    setup: function (editor) {
                        $this.setState({
                            editor,
                            id: $this.props.id
                        })
                        editor.on('keyup change', function (e) {
                            const content = editor.getContent();
                            $this.props.onContentChange(content, $this.props.name);
                        });
                    },
                    height:300
                },
                quicktags: true,
                mediaButtons: true
            });
        }

    }

    render() {
        const name = this.props.name?this.props.name:'';
        return (
            <textarea id={this.props.id} value={this.props.value} onChange={(e) => this.props.onContentChange(e.target.value)} name={name} />
        )
    }
}

export default WPEditor;