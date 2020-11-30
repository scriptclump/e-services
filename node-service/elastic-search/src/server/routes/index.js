const express    = require("express");
const controller = require("../controllers");
const routes     = express.Router();

routes.route("/search-all").post(controller.getDetails);
routes.route("/suggestions").post(controller.getSuggestion);
routes.route("/new").post(controller.addElement);

module.exports = routes;