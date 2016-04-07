/**
 * 
 */
function ImportViewModel () {
    
}

function InstallerViewModel () {
    var self = this;
    self.step_zero_visible = ko.observable(true);
    self.step_one_visible = ko.observable(false);
    self.step_two_visible = ko.observable(false);
    self.step_three_visible = ko.observable(false);
    self.step_four_visible = ko.observable(false);
    self.step_five_visible = ko.observable(false);
    
    self.app_name = ko.observable();
    self.app_auth = ko.observable();
    self.app_desc = ko.observable();
    
    self.db_host = ko.observable();
    self.db_type = ko.observable('mysql');
    self.db_name = ko.observable();
    self.db_char = ko.observable('utf8');
    self.db_user = ko.observable();
    self.db_pass = ko.observable();
    self.invalid_step_two = ko.observable(false);
    
    self.loc_time = ko.observable();
    self.loc_lang = ko.observable();
    
    self.email = ko.observable();
    self.email_host = ko.observable();
    self.email_port = ko.observable();
    self.email_prot = ko.observable();
    self.email_auth = ko.observable();
    self.email_user = ko.observable();
    self.email_pass = ko.observable();
    self.email_name = ko.observable();
    self.email_addr = ko.observable();
    
    self.email_auth_text = ko.computed(function() {
	return (self.email_auth() == 1) ? 'Yes' : 'No';
    }, this);
    
    self.email_auth_bool = ko.computed(function() {
	return (self.email_auth() == 1) ? true : false;
    }, this);
    
    self.show_step = function (step_number) {
	self.step_zero_visible(false);
	self.step_one_visible(false);
	self.step_two_visible(false);
	self.step_three_visible(false);
	self.step_four_visible(false);
	self.step_five_visible(false);
	
	switch (step_number) {
		case 1:
		    self.step_one_visible(true);
		    break;
		case 2:
		    self.step_two_visible(true);
		    break;
		case 3:
		    self.step_three_visible(true);
		    break;
		case 4:
		    self.step_four_visible(true);
		    break;
		case 5:
		    self.step_five_visible(true);
		    break;
		case 0:
		default:
		    self.step_zero_visible(true);
	}
    }
    
    self.show_next_step = function () {
	if (self.step_zero_visible()) {
	    self.step_zero_visible(false);
	    self.step_one_visible(true);
	} else if (self.step_one_visible()) {
	    self.step_one_visible(false);
	    self.step_two_visible(true);
	} else if (self.step_two_visible()) {
	    self.step_two_visible(false);
	    self.step_three_visible(true);
	} else if (self.step_three_visible()) {
	    self.step_three_visible(false);
	    self.step_four_visible(true);
	} else if (self.step_four_visible()) {
	    self.step_four_visible(false);
	    self.step_five_visible(true);
	}
    };
    
    self.validate_step_one = function (element) {
	self.step_one_visible(false);
	self.step_two_visible(true);
    };
    
    self.validate_step_two = function (element) {
	json_array = new Array();
	json_array['host'] = self.db_host;
	json_array['type'] = self.db_type;
	json_array['char'] = self.db_char;
	json_array['name'] = self.db_name;
	json_array['user'] = self.db_user;
	json_array['pass'] = self.db_pass;
	$.ajax("/install/test_database", {
	    data: ko.toJSON(json_array),
	    type: "get", contentType: "application/json",
	    success: function (result) { console.log(result); },
	    fail: function (result) { console.log(result) }
	})
	
	//self.step_one_visible(false);
	//self.step_two_visible(true);
    };
    
}

ko.applyBindings(new InstallerViewModel());
