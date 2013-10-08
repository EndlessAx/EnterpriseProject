EnterpriseProject
=================

Server-side modifications to Perception

This is an overview of the structure of the Perception web application

    a. Folder structure
    b. Application flow
    c. "control.php" explained


a. Folder Structure

/WebContent
    /controller
    /model
    /view
    
/facebook-sdk


"/WebContent" contains the core of the web application
  "/controller" contains the modules relating to the application layer
    User Module, Search Module and Course Management System modules are located here.
    
  "/model" contains code relating to data access (used by application layer)
    User Module SQL schema and User Module data access objects are located here.
  
  "/view" contains files relating to the presentation layer
    HTML, CSS and JS files go here.

"/facebook-sdk" contains the facebook APIs required to enable facebook login


b. Application flow

Perception uses a "Model-View-Controller" structure.
  1. User input generated in the "view" is sent to the "controller"
  
  2. The "controller" directs the input to the relevant application logic for processing
     The application logic updates the "model" with the user input.
     
  3. The application logic updates the "view" based off the updated "model".
  
This is applied in Perception through the use of the "control.php" file, found at:
"WebContent/Controller/control.php"

  1. Any user input from the "view" (HTML/CSS/JS) pages of the application is sent to "control.php".
  
  2. "control.php" directs the user input (based off a "command" sent in the HTTP request) to the relevant
     application logic modules for processing - found in "/WebContent/controlller/functions".
    
  3. The application logic modules update the "view" by changing or updating the HTML page displayed.
  

c. "control.php" explained
  "control.php" accepts the HTTP request from the displayed webpage and parses its query string.
  
  It uses the "user_name" and "user_pasword" cookies to determine user login status.
  It uses the "command" parameter to determine the application flow.
  
  The "command" parameter is in the format c_<page>_<command>_<id(optional)>
  Examples include:
    c_header_login
    c_header_register
    c_home_search
    c_register_normal
    c_results_info_2144
  
    
    



  

   
