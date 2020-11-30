/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_ship_track_details', {
    gds_ship_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_ship_grid_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'gds_orders',
        key: 'gds_order_id'
      }
    },
    ship_fname: {
      type: DataTypes.STRING(30),
      allowNull: false
    },
    ship_lname: {
      type: DataTypes.STRING(30),
      allowNull: false
    },
    ship_company: {
      type: DataTypes.STRING(75),
      allowNull: false
    },
    ship_addr1: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    ship_addr2: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    ship_city: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    ship_postcode: {
      type: DataTypes.STRING(10),
      allowNull: false
    },
    ship_country_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ship_state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ship_method: {
      type: DataTypes.STRING(30),
      allowNull: false
    },
    ship_service_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    tracking_id: {
      type: DataTypes.STRING(30),
      allowNull: false
    },
    vehicle_number: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    rep_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    contact_number: {
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
    tableName: 'gds_ship_track_details'
  });
};
