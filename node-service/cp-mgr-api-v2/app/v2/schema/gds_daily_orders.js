/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_daily_orders', {
    id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    ff_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    total: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    date: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'gds_daily_orders'
  });
};
