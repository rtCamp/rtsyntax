// library to highlight the code
let hljs = require('./highlight.js');


// Initialize the worker
let worker = null;

// Initialize the debug
let debug = false;
if (highlight_obj.debug === '1') {
	debug = true;
}

// Initialize required components
const {Component} = wp.element;
const {SelectControl} = wp.components;
const {__} = wp.i18n;

// Edit class for edit method of registeredBlockType
class Edit extends Component {

	// Constructor
	constructor() {

		super(...arguments);

		// Init states
		this.state = {
			updateMessage: null,
			status       : '',
		};

		// Bind methods
		this.changeLanguage = this.changeLanguage.bind(this);
		this.setAreaHeight = this.setAreaHeight.bind(this);
		this.changeContent = this.changeContent.bind(this);
		this.updateContent = this.updateContent.bind(this);
		this.handleTabKey = this.handleTabKey.bind(this);
		this.getHighlight = this.getHighlight.bind(this);
	}

	setAreaHeight(e) {
		this.props.setAttributes({
			areaHeight: e.target.scrollHeight + 'px'
		});
	}


	changeContent(e){
		this.props.setAttributes({
			content: e.target.value,
			areaHeight: e.target.scrollHeight + 'px',
		});
	}


	// Prevent default tab behaviour in code textarea
	handleTabKey(e) {

		this.setAreaHeight(e);

		if (!e.keyCode || e.keyCode !== 9) {
			return;
		}

		e.preventDefault();
		e.stopPropagation();

		let textarea = e.target;
		let newCaretPosition = textarea.selectionStart + "\t".length;
		textarea.value = textarea.value.substring(0, textarea.selectionStart) + "\t" + textarea.value.substring(textarea.selectionStart, textarea.value.length);
		textarea.selectionStart = newCaretPosition;
		textarea.selectionEnd = newCaretPosition;
		textarea.focus();

		this.props.setAttributes({
			content: e.target.value,
		});
		// this.changeContent(e);

	}


	// Change code language to highlight
	changeLanguage(e) {

		this.props.setAttributes(
			{
				language: e,
			}
		);

	}

	// get highlighted code using highlight.js
	getHighlight(val = null, cb = null) {
		const {attributes, setAttributes} = this.props;

		let content = val !== null ? val : attributes.content;

		if (typeof(Worker) !== "undefined" && worker === null) {
			worker = new Worker(highlight_obj.path+'/js/highlight_worker.build.js');
		}


		if (worker !== null) {

			// console.log('posting msg: '+attributes.language);
			worker.postMessage({
				language: attributes.language,
				content : content,
			});

			worker.onmessage = (event) => {

				if( event.data.value.length !== 0 ){
					content = <div
						className={'hljs'}
						dangerouslySetInnerHTML={{
							__html: event.data.value
						}}
					/>;

					content = [content];
				} else {
					content = [];
				}


				if (val !== null) {
					cb(val, content);
				} else {
					setAttributes(
						{
							html_content: [content]
						}
					);
				}


				if (debug) {
					this.setState({
						updateMessage: __('The code has been highlighted!', 'rtSyntax'),
						status       : 'success'
					});

					setTimeout(
						() => {
							this.setState({
								updateMessage: null
							});
						},
						3000
					);
				}


			};

			if (debug) {
				this.setState({
					updateMessage: __('Highlighting the new code! Please wait...', 'rtSyntax'),
					status       : 'danger',
				});
			}

			worker = null;

		} else {

			if( event.data.value.length !== 0 ){
				content = <code
					className={'hljs'}
					dangerouslySetInnerHTML={{
						__html: event.data.value
					}}
				/>;

				content = [content];
			} else {
				content = [];
			}

			if (val) {
				cb(val, content);
			} else {
				setAttributes(
					{
						html_content: [content]
					}
				);
			}

		}

	}


	// Save content when content is changed in code text-area
	updateContent() {

		const {setAttributes, attributes} = this.props;

		this.getHighlight(
			attributes.content,
			(value, content) => {
				setAttributes(
					{
						content     : value,
						html_content: content
					}
				);
			}
		);

	}


	// Render the output of edit method
	render() {

		const {attributes, isSelected} = this.props;
		const {state} = this;

		// list of languages from highlight.js
		let languages = hljs.listLanguages();
		languages = languages.map(
			(language) => {
				return {
					label: language,
					value: language,
				};
			}
		);

		let updateMessage = state.updateMessage && debug ? (
			<div style={{padding: '5px', marginBottom: '5px'}} className={'alert alert-' + state.status}>
				{state.updateMessage}
			</div>
		) : '';

		let showContent = attributes.html_content!== undefined && attributes.html_content.length !== undefined && attributes.html_content.length > 0;

		// return content
		return [

			// Textarea to edit code, only show when in focus {__('Selected Language','rtSyntax')}: {attributes.language}
			isSelected && (
				<div
					tabIndex={'1'}
					onBlur={this.updateContent}
				>
					<SelectControl
						label={__('Language', 'rtSyntax')}
						value={attributes.language}
						options={languages}
						onChange={this.changeLanguage}
					/>
					<textarea
						onFocus={this.setAreaHeight}
						onChange={this.changeContent}
						onKeyDown={this.handleTabKey}
						className={'form-control'}
						style={{width: '100%', height: this.props.attributes.areaHeight}}
						placeholder={__("Enter code here", 'rtSyntax') + " ... "}
					>
						{attributes.content}
					</textarea>

				</div>
			),

			// Show actual highlighted code when not in focus
			!isSelected && (
				<div>
					<pre>
						{showContent ? updateMessage : ''}
						{showContent ? attributes.html_content : 'Click here to add code ....'}
					</pre>
				</div>
			)
		];
	}


	componentWillUnmount() {

		worker = null;

	}

}

export default Edit;
