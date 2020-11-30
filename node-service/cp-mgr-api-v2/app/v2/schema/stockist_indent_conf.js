/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('stockist_indent_conf', {
    sic_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    stock_days: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    stock_value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    security_deposit: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    stock_norm: {
      type: DataTypes.DECIMAL,
      allowNull: false
    }
  }, {
    tableName: 'stockist_indent_conf'
  });
};
