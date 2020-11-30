/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('allOrderswithcomments', {
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    order_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    orderstatus: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    order value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    hub: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    invoice_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    invoicedate: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    invoicevalue: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    SUM(gds_order_products.`qty`): {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    commenttype: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    comment_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    commentstatus: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    commentby: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    comment: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'allOrderswithcomments'
  });
};
