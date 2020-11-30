/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('routing_admin_crates_log', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    crate_code: {
      type: DataTypes.STRING(50),
      allowNull: false,
      unique: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    order_code: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    route_admin_log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '1'
    },
    date_time: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'routing_admin_crates_log'
  });
};
