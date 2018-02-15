var hljs = require( 'highlight.js' );
hljs.configure({
    useBR: true
});
hljs.initHighlightingOnLoad();
const  { InspectorControls } = wp.blocks;
const  { __ } = wp.i18n;

class Highlighter extends React.Component {

    constructor() {
        super(...arguments);

        this.onChangeContent = this.onChangeContent.bind(this);
        this.handleTabKey = this.handleTabKey.bind(this);
        this.selectChange = this.selectChange.bind( this );
        // this.selectChangeTheme = this.selectChangeTheme.bind( this );
    }

    handleTabKey(e){
        if( e.keyCode && e.keyCode === 9 ){
            e.preventDefault();
            e.stopPropagation();
            let textarea = e.target;
            let newCaretPosition = textarea.selectionStart + "\t".length;
            textarea.value = textarea.value.substring(0, textarea.selectionStart) + "\t" + textarea.value.substring(textarea.selectionStart, textarea.value.length);
            textarea.selectionStart = newCaretPosition;
            textarea.selectionEnd = newCaretPosition;
            textarea.focus();
        }
    }

    onChangeContent( newContent ) {
        this.props.setAttributes( { content: newContent.target.value } );

        let attributes = this.props.attributes;

        let html = [];
        if( attributes.content && attributes.language ){
            let content = attributes.content.split('\n');
            for( let i in content ){

                let a = hljs.highlight(attributes.language,content[i]).value;
                a=a.replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');

                html.push(
                    <div key={i+'_div'} dangerouslySetInnerHTML={{
                        __html: a+'<br/>'
                    }} />
                );
            }
            this.props.setAttributes({raw_content: html});
        }
    }

    render() {
        const attributes = this.props.attributes;
        const focus = this.props.focus;
        const isSelected = this.props.isSelected;
        const lang = attributes.language;
        // let themes = rtsyntax.themes;

        let languages = hljs.listLanguages();
        const optionsItem = languages.map( (language,index) =>
            <option key={index+'_option'}>{ language.toString() }</option>
        );
        // const listThemes = themes.map( (theme,index) =>
        //     <option key={index+'_option'}>{ theme.toString() }</option>
        // );

        const inspectorControls = focus && (
            <InspectorControls key="inspector">
                <h3>{ __( 'Select Programming Language' ) }</h3>
                <select className="language-list" onChange={ this.selectChange.bind(this) } value={lang}>
                    { optionsItem }
                </select>
                {/*<h3>{ __( 'Select Themes' ) }</h3>*/}
                {/*<select onChange={ this.selectChangeTheme.bind(this) } value={attributes.theme}>*/}
                    {/*{ listThemes }*/}
                {/*</select>*/}
            </InspectorControls>
        );
        
        // const link = <link rel="stylesheet" href={ rtsyntax.path + 'css/' + attributes.theme + '.css' } />;
        const langList = <select className="language-list" onChange={ this.selectChange.bind(this) } value={lang}>{ optionsItem }</select>;

        return[
            // link,
            inspectorControls,
            <div className="rtsyntax-header">{'Selected Language: '} <span style={{ cursor: 'pointer' }}>{langList}</span></div>,
            <br />,
            isSelected && (
                <textarea
                    autoFocus={true}
                    className='code-txtarea'
                    onBlur={this.onChangeContent}
                    onChange={this.onChangeContent}
                    onFocus={this.onChangeContent}
                    onKeyUp={this.handleTabKey}
                    rows={10}
                    style={{ width: '100%',  }}
                >
                    { attributes.content }
                </textarea>
            ),
            !isSelected && (
                <div className="hljs">{attributes.raw_content}</div>
            )
        ];
    }

    selectChange( e ) {
        this.props.setAttributes( { language: e.target.value } );
    }

    // selectChangeTheme( e ) {
    //     this.props.setAttributes( { theme: e.target.value } );
    // }

}

export default Highlighter;
