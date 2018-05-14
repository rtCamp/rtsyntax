// Initialize required components
const {Component} = wp.element;

/**
 * Save class for save method of registeredBlockType
 */
class Save extends Component {

	/**
	 * Constructor
	 */
	constructor() {

		super(...arguments);

	}

	/**
	 * Render the output of save method
	 *
	 * @returns jsx
	 */
	render() {
		const {attributes} = this.props;
		return <pre className={attributes.language}>{attributes.content}</pre>;
	}

}

export default Save;