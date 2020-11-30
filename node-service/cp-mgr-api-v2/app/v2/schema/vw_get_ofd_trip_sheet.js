/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_get_ofd_trip_sheet', {
    st_del_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    wh_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    wh_address1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    wh_address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    wh_city: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    wh_phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    wh_contact_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    wh_pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    lp_wh_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    st_vehicle_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    st_driver_name: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    st_driver_mobile: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    beat_area: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    order_code: {
      type: DataTypes.STRING(16),
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
    st_docket_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    crates_id: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    weight: {
      type: "DOUBLE(22,5)",
      allowNull: true
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_get_ofd_trip_sheet'
  });
};
