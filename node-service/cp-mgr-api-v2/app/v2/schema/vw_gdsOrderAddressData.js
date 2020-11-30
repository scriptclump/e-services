/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_gdsOrderAddressData', {
    gds_addr_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    fname: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    address_type: {
      type: DataTypes.STRING(20),
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
    country_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    state_name: {
      type: DataTypes.STRING(255),
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
    }
  }, {
    tableName: 'vw_gdsOrderAddressData'
  });
};
