/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_brand_details', {
    brand_log: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    brand_name: {
      type: DataTypes.STRING(75),
      allowNull: false
    },
    manufacturer_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    manufacturer_logo: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    IS Trademarked: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    Authorised: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    products: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    withImages: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    withoutimages: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    withinventory: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    withoutinventory: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pending: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_brand_details'
  });
};
