/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_products_freshness_by_fc_summary', {
    FC ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    FC NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    VALUE: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_products_freshness_by_fc_summary'
  });
};
