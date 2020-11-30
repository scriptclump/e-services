/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_brandwisedetails', {
    brand_logo: {
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
    Products: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    WithImages: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    WithoutImages: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    withInventory: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    WithoutInventory: {
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
    tableName: 'vw_brandwisedetails'
  });
};
