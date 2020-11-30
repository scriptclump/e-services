/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_productsmargin', {
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    image: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    category_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    base_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    EBP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    RBP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    CBP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inventorymode: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Schemes: {
      type: DataTypes.CHAR(0),
      allowNull: false,
      defaultValue: ''
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    MBQ: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prod_price_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_productsmargin'
  });
};
