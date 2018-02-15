// Initialize required components
const {Component} = wp.element;

// Save class for save method of registeredBlockType
class Save extends Component {

	// Constructor
	constructor() {

		super(...arguments);

	}

	// Render the output of save method
	render() {
		return <pre>{this.props.attributes.html_content}</pre>;
	}

}

export default Save;