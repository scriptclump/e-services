/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_get_trip_sheet_hub_to_dc', {
    hub_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    wh_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hub_address1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hub_address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hub_city: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    hub_phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    hub_contact_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    hub_pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    st_del_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    st_vehicle_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    st_driver_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    st_driver_mobile: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    beat_area: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoice_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    shop_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    st_docket_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    cfc_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    crates_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    bags_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    pname: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    crates_id: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    weight: {
      type: "DOUBLE(22,5)",
      allowNull: true
    }
  }, {
    tableName: 'vw_get_trip_sheet_hub_to_dc'
  });
};
