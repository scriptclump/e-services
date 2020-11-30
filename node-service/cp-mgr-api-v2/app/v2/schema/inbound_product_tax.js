/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inbound_product_tax', {
    inbound_tax_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inbound_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'inbound_product',
        key: 'inbound_product_id'
      }
    },
    tax_class: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax_percent: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'inbound_product_tax'
  });
};
