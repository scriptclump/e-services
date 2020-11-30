/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('customer_discounts', {
    customer_discountid: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    discount_type: {
      type: DataTypes.ENUM('value','percentage'),
      allowNull: true
    },
    discount_on: {
      type: DataTypes.ENUM('star','product','order'),
      allowNull: true
    },
    discount_on_values: {
      type: "DOUBLE(16,4)",
      allowNull: true
    },
    discount: {
      type: "DOUBLE",
      allowNull: true
    },
    priority: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    discount_start_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    discount_end_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    discount_noof_times: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'customer_discounts'
  });
};
