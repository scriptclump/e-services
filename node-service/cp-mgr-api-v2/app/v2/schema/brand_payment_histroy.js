/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('brand_payment_histroy', {
    brand_pay_id: {
      type: DataTypes.INTEGER(15),
      allowNull: false,
      primaryKey: true
    },
    config_mapping_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    clicks: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    click_cost: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    click_amt: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    impressions: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    impression_cost: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    impression_amt: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    total_amt_paid: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    bu_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    supplier_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    pay_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    item_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    }
  }, {
    tableName: 'brand_payment_histroy'
  });
};
