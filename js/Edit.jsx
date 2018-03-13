// get hljs from highlight library
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

		// set area height of textarea
		let temp_txtarea = document.createElement('textarea');
		temp_txtarea.value = this.props.attributes.content;
		document.body.appendChild(temp_txtarea);



		// Init states
		this.state = {
			updateMessage : false,
			status        : '',
			html_content  : '',
			content       : this.props.attributes.content,
			contentChanged: true,
			language      : this.props.attributes.language,
			start         : true,
			areaHeight    : temp_txtarea.scrollHeight + 'px',
		};

		// remove temporary textarea
		document.body.removeChild(temp_txtarea);


		// Bind methods
		this.changeLanguage = this.changeLanguage.bind(this);
		this.setAreaHeight = this.setAreaHeight.bind(this);
		this.changeStateContent = this.changeStateContent.bind(this);
		this.handleTabKey = this.handleTabKey.bind(this);
		this.getHighlightedCode = this.getHighlightedCode.bind(this);

	}


	// handle change of content which is changed everytime
	changeStateContent(e) {

		this.setState({
			content       : e.target.value,
			areaHeight    : e.target.scrollHeight + 'px',
			contentChanged: true,
		});

	}


	// set area height when textarea is focused
	setAreaHeight(e) {
		this.setState({
			areaHeight    : e.target.scrollHeight + 'px',
			contentChanged: true,
		});
	}


	// Prevent default tab behaviour in code textarea
	handleTabKey(e) {

		if (!e.keyCode || e.keyCode !== 9) {
			this.setAreaHeight(e);
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

		this.setState({
			content       : textarea.value,
			contentChanged: true,
		});

	}

	// Change code language to highlight
	changeLanguage(e) {

		this.setState(
			{
				language      : e,
				contentChanged: true,
			}
		);

	}

	// get highlighted code using highlight.js
	getHighlightedCode(content = null, language = null, cb = null) {
		const {state} = this;

		content = content === null ? state.content : content;
		language = language === null ? state.language : language;
		let html_content = '';

		if (typeof(Worker) !== "undefined" && worker === null) {
			worker = new Worker(highlight_obj.path + '/js/highlight_worker.build.js');
		}

		if (worker !== null) {

			worker.postMessage({
				language: language,
				content : content,
			});

			worker.onmessage = (event) => {

				if (event.data.value.length !== 0) {
					html_content = <div
						className={'hljs'}
						style={{maxHeight:'60em'}}
						dangerouslySetInnerHTML={{
							__html: event.data.value
						}}
					/>;
					html_content = [html_content];
				} else {
					html_content = [];
				}

				if (typeof(cb) === 'function') {
					cb(true, event.data.language, content, html_content, true);
				}


			};

			if (typeof(cb) === 'function') {
				cb(false, null, null, true);
			}

		} else {

			let nonworker_content = hljs.highlight(language, content);

			if (nonworker_content.value.length !== 0) {
				html_content = <div
					className={'hljs'}
					style={{maxHeight:'60em'}}
					dangerouslySetInnerHTML={{
						__html: nonworker_content.value
					}}
				/>;
				html_content = [html_content];
			} else {
				html_content = [];
			}

			if (typeof(cb) === 'function') {
				cb(true, language, content, html_content, false);
			}

		}

	}


	// update attribute content and highlight code
	updateAttrContent() {

		const {setAttributes} = this.props;
		const {state} = this;

		this.getHighlightedCode(state.content, state.language, (status, language = null, content = null, html_content = null, worker) => {

			if (!status) {

				this.setState((prevState) => {

					if (prevState.start) {
						return {
							contentChanged: false,
							start         : false,
						};
					} else {
						return {
							updateMessage : __('The code is being highlighted! Please wait...', 'rtsyntax'),
							status        : 'danger',
							contentChanged: false,
						}
					}

				});

				return;
			}

			this.setState((prevState) => {

				if (prevState.status === 'danger') {
					setTimeout(() => {

						this.setState({
							updateMessage : false,
							status        : '',
							contentChanged: false,
						});

					}, 3000);

					return {
						html_content  : html_content,
						contentChanged: false,
						updateMessage : __('The code has been highlighted!', 'rtsyntax'),
						status        : 'success',
					};
				}

				return {
					contentChanged: false,
					html_content  : html_content,
				};

			});
			setAttributes({
				content : state.content,
				language: state.language,
			});


		});

	}


	// Render the output of edit method
	render() {

		const {attributes, isSelected} = this.props;
		const {state} = this;


		// When focus is removed, highlight the code
		if (!isSelected && state.contentChanged) {
			this.updateAttrContent();
		}

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
			<div style={{
				padding        : '5px',
				marginBottom   : '5px',
				backgroundColor: state.status === 'danger' ? '#ff000085' : 'rgba(5, 162, 104, 0.71)',
				color          : 'white',
				borderRadius   : '4px',
				paddingLeft    : '1.2em'
			}}>
				{state.updateMessage}
			</div>
		) : '';

		let showContent = state.html_content !== undefined && state.html_content.length !== undefined && state.html_content.length > 0;

		// return content
		return [

			// Textarea to edit code, only show when in focus
			isSelected && (
				<div className={'rtsyntax-edit'}>
					<SelectControl
						label={__('Language:', 'rtsyntax')}
						value={state.language}
						options={languages}
						onChange={this.changeLanguage}
					/>
					<textarea
						onFocus={this.setAreaHeight}
						onChange={this.changeStateContent}
						onKeyDown={this.handleTabKey}
						className={'form-control'}
						style={{width: '100%', maxHeight: '60em' , minHeight: '20em', height: state.areaHeight}}
						placeholder={__("Enter code here", 'rtsyntax') + " ... "}
					>
						{state.content}
					</textarea>

				</div>
			),

			// Show actual highlighted code when not in focus
			!isSelected && (
				<pre>
					<code>
						{showContent ? updateMessage : ''}
						{showContent ? state.html_content : 'Click here to add code ....'}
					</code>
				</pre>
			)
		];
	}

	componentWillUnmount() {

		worker = null;

	}

}

export default Edit;
