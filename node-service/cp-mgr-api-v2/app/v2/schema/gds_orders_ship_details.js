/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_orders_ship_details', {
    order_ship_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0',
      references: {
        model: 'gds_orders',
        key: 'gds_order_id'
      }
    },
    ship_method: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    ship_service_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tracking_id: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    erp_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    fname: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    mname: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    lname: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    mobile: {
      type: DataTypes.STRING(15),
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
      type: DataTypes.STRING(75),
      allowNull: true
    },
    postcode: {
      type: DataTypes.STRING(10),
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
    telephone: {
      type: DataTypes.STRING(15),
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
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'gds_orders_ship_details'
  });
};
