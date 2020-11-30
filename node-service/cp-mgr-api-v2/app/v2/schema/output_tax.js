/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('output_tax', {
    output_tax_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    outward_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_no: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    transaction_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    tax_type: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    tax_percent: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
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
    tableName: 'output_tax'
  });
};
