/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_incorrect_po', {
    PO ID: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false
    },
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Unit Price: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    MRP: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    PO Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Sub Total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_incorrect_po'
  });
};
