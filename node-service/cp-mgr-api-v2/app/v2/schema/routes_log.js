/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('routes_log', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    route_admin_log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delivery_executive_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    data: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_on: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'routes_log'
  });
};
