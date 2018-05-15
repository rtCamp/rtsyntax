// get hljs from highlight library
let hljs = require('./highlight.js');

// Initialize the worker
let worker = null;

// Initialize the debug
let debug = false;
if ('1' === rtSyntax.debug) {
	debug = true;
}

// Initialize required components
const {Component} = wp.element;
const {SelectControl} = wp.components;
const {__} = wp.i18n;

/**
 * Edit class for edit method of registeredBlockType
 */
class Edit extends Component {

	/**
	 * Constructor
	 */
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


	/**
	 * handle change of content which is changed everytime
	 *
	 * @param e: event object
	 */
	changeStateContent(e) {

		this.setState({
			content       : e.target.value,
			areaHeight    : e.target.scrollHeight + 'px',
			contentChanged: true,
		});

	}


	/**
	 * set area height when textarea is focused
	 *
	 * @param e: event object
	 */
	setAreaHeight(e) {
		this.setState({
			areaHeight    : e.target.scrollHeight + 'px',
			contentChanged: true,
		});
	}


	/**
	 * Prevent default tab behaviour in code textarea
	 *
	 * @param e: event object
	 */
	handleTabKey(e) {

		if (!e.keyCode || 9 !== e.keyCode) {
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

	/**
	 * Change code language to highlight
	 *
	 * @param e: event object
	 */
	changeLanguage(e) {

		this.setState({
			language      : e,
			contentChanged: true,
		});

	}

	/**
	 * get highlighted code using highlight.js
	 *
	 * @param content: content to highlight
	 * @param language: highlight language
	 * @param callback: callback function to perform actions after highlight
	 */
	getHighlightedCode(content = null, language = null, callback = null) {
		const {state} = this;

		content = null === content ? state.content : content;
		language = null === language ? state.language : language;
		let html_content = '';

		if (typeof(Worker) !== "undefined" && null === worker) {
			worker = new Worker(rtSyntax.path + '/js/highlight_worker.build.js');
		}

		if (null !== worker) {

			worker.postMessage({
				language: language,
				content : content,
			});

			worker.onmessage = (event) => {

				if (event.data.value.length !== 0) {
					html_content = <div
						className='hljs'
						dangerouslySetInnerHTML={{
							__html: event.data.value
						}}
					/>;
					html_content = [html_content];
				} else {
					html_content = [];
				}

				if (typeof(callback) === 'function') {
					callback(true, event.data.language, content, html_content, true);
				}


			};

			if (typeof(callback) === 'function') {
				callback(false, null, null, true);
			}

		} else {

			let nonworker_content = hljs.highlight(language, content);

			if (nonworker_content.value.length !== 0) {
				html_content = <div
					className='hljs'
					dangerouslySetInnerHTML={{
						__html: nonworker_content.value
					}}
				/>;
				html_content = [html_content];
			} else {
				html_content = [];
			}

			if (typeof(callback) === 'function') {
				callback(true, language, content, html_content, false);
			}

		}

	}


	/**
	 * update attribute content and highlight code
	 */
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

				if ('danger' === prevState.status) {
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


	/**
	 * Render the output of edit method
	 *
	 * @returns array
	 */
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
			<div className='rtsyntax-code-update-message' style={{
				backgroundColor: 'danger' === state.status ? '#ff000085' : 'rgba(5, 162, 104, 0.71)',
			}}>
				{state.updateMessage}
			</div>
		) : '';

		let showContent = undefined !== state.html_content && state.html_content.length !== undefined && state.html_content.length > 0;

		// return content
		return [

			// Textarea to edit code, only show when in focus
			isSelected && (
				<div className='rtsyntax-edit'>
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
						className='form-control'
						style={{height: state.areaHeight}}
						placeholder={__("Enter code here", 'rtsyntax') + " ... "}
					>
						{state.content}
					</textarea>

				</div>
			),

			// Show actual highlighted code when not in focus
			!isSelected && (
				<pre className='rtsyntax-admin-pre'>
					<code>
						{showContent ? updateMessage : ''}
						{showContent ? state.html_content : __('Click here to add code ', 'rtsyntax') + '....'}
					</code>
				</pre>
			)
		];
	}

	/**
	 * Function to free worker object
	 */
	componentWillUnmount() {

		worker = null;

	}

}

export default Edit;
