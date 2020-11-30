/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_freebie_return_products', {
    Returned Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    FreeBie Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Freebie Desc: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    Return Created Date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    },
    DE Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Returned Freebie: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    Exptect Freebies: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_freebie_return_products'
  });
};
