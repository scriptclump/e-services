/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_gds_order_add_details', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    name: {
      type: DataTypes.STRING(77),
      allowNull: true
    },
    company: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    addr1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    addr2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    postcode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    suffix: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    telephone: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    mobile: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    country_name: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    state_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'vw_gds_order_add_details'
  });
};
