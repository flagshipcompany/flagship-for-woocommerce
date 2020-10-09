var packageBoxesApp = new Vue({
	el: '#flagship_package_boxes',
	data: {
		get_boxes_url: null,
		save_boxes_url: null,
		boxes: [],
		box_form_default: {
			model: '',
			length: null,
			width: null,
			width: null,
			max_weight: null,
			extra_charge: null
		},
		box_form: {},
		data_saved: false,
		invalid_data: false,
		save_error: false,
	},
	methods: {
		init: function() {
			this.get_boxes_url  = this.$refs.getBoxesUrl.textContent;
			this.save_boxes_url  = this.$refs.saveBoxesUrl.textContent;
			this.box_form = Object.assign({}, this.box_form_default);

			this.fetchBoxes();
        },
        fetchBoxes: function() {
        	var $vm = this;        	
			axios.get(this.get_boxes_url)
                .then(response => {
                   this.boxes = JSON.parse(response.data);
                })
                .catch(error => {
                });
        },
        saveBoxes: function() {
        	var requestData = this.createBoxesData(this.boxes, this.box_form);

        	if (requestData.length == 0) {
        		return;
        	}

        	axios.post(this.save_boxes_url, requestData)
                .then(response => {
                   this.boxes = JSON.parse(response.data);
                   this.box_form = Object.assign({}, this.box_form_default);
                   this.showMessage('saved');
                })
                .catch(error => {
                	this.showMessage('error');
                });
        },
        removeBox: function(id) {
        	this.boxes = this.boxes.filter(function(val) {
        		return val.id != id;
        	});
        },
        createBoxesData: function(boxes, newBox) {
        	var boxesCp = this.mergeNewBox(boxes, newBox);
        	boxesValid = this.validateBoxes(boxesCp);

        	if (!boxesValid) {
        		this.showMessage('invalid');

        		return [];
        	}

        	return boxesCp;        	
        },
        mergeNewBox: function(boxes, newBox) {
        	if (!newBox.model && !newBox.length && !newBox.width && !newBox.height && !newBox.max_weight && !newBox.extra_charge) {
        		return boxes;
        	}

        	var maxId = boxes.reduce(function(accumulator, currentValue, currentIndex, array) {
        		return accumulator < currentValue.id ? currentValue.id : accumulator;
        	}, 0);
        	var nextId = maxId + 1;

        	var newBoxCp = Object.assign({}, newBox);
        	newBoxCp.id = nextId;
        	var boxesCp = boxes.slice(0);
        	boxesCp.push(newBoxCp);

        	return boxesCp;
        },
        validateBoxes: function(boxes) {
        	var validBoxes = boxes.filter(function(val) {
        		return val.model && val.length >= 0 && val.width > 0 && val.height > 0 && val.max_weight > 0;
        	});

        	return validBoxes.length == boxes.length;
        },
        showMessage: function(type) {
        	var self = this;

        	switch(type) {
			  	case 'saved':
				    self.data_saved = true;
				    setTimeout(function(){
		                self.data_saved = false;
		            }, 2000);
				    break;
			  	case 'invalid':
				    self.invalid_data = true;
				    setTimeout(function(){
		                self.invalid_data = false;
		            }, 2000);
				    break;
			    case 'error':
				    self.save_error = true;
				    setTimeout(function(){
		                self.save_error = false;
		            }, 2000);
				    break;
			}

			window.scrollTo(500, 0); 
        }
	},
    mounted: function() {
        this.init();
    }
});
